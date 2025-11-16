<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use App\Events\AttendanceRecorded;
use Illuminate\Support\Facades\Cache;


class AttendanceService
{
    /**
     * Bulk record attendance for a class/section on a specific date.
     * $data = [
     *    ['student_id' => 1, 'status' => 'present', 'note' => null],
     *    ...
     * ]
     */
    public function recordBulk(string $date, string $class, ?string $section, array $attendanceData, $recordedBy = null)
    {
        return DB::transaction(function () use ($date, $class, $section, $attendanceData, $recordedBy) {

            // Get the students for validation
            $students = Student::query()
                ->where('class', $class)
                ->when($section, fn($q) => $q->where('section', $section))
                ->pluck('id')
                ->toArray();

            foreach ($attendanceData as $entry) {
                if (!in_array($entry['student_id'], $students)) {
                    throw new \Exception("Invalid student_id: {$entry['student_id']} for class/section.");
                }

                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $entry['student_id'],
                        'date'       => $date,
                    ],
                    [
                        'status'      => $entry['status'],
                        'note'        => $entry['note'] ?? null,
                        'recorded_by' => $recordedBy,
                    ]
                );

                // Fire event for each record
                AttendanceRecorded::dispatch($attendance);
            }

            return true;
        });
    }

    /**
     * Generate monthly attendance for a class.
     */
    public function getMonthlyReport(string $month, string $class)
    {
        return Attendance::with('student')
            ->where('date', 'like', "$month%")  // YYYY-MM%
            ->whereHas('student', function ($q) use ($class) {
                $q->where('class', $class);
            })
            ->get();
    }

    public function getDailyStats(string $date, ?string $class = null, ?string $section = null): array
    {
        $cacheKey = sprintf(
            'attendance_stats:%s:%s:%s',
            $date,
            $class ?? 'all',
            $section ?? 'all'
        );

        // Cache for 10 minutes
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($date, $class, $section) {

            $studentQuery = Student::query();

            if ($class) {
                $studentQuery->where('class', $class);
            }

            if ($section) {
                $studentQuery->where('section', $section);
            }

            $totalStudents = $studentQuery->count();

            $attendanceQuery = Attendance::query()
                ->whereDate('date', $date)
                ->when($class, function ($q) use ($class) {
                    $q->whereHas('student', fn($sq) => $sq->where('class', $class));
                })
                ->when($section, function ($q) use ($section) {
                    $q->whereHas('student', fn($sq) => $sq->where('section', $section));
                });

            $counts = $attendanceQuery
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $present = $counts['present'] ?? 0;
            $absent  = $counts['absent'] ?? 0;
            $late    = $counts['late'] ?? 0;
            $marked  = $present + $absent + $late;

            $presentPercent = $totalStudents > 0
                ? round(($present / $totalStudents) * 100, 2)
                : 0;

            return [
                'date'            => $date,
                'class'           => $class,
                'section'         => $section,
                'total_students'  => $totalStudents,
                'marked'          => $marked,
                'present'         => $present,
                'absent'          => $absent,
                'late'            => $late,
                'present_percent' => $presentPercent,
            ];
        });
    }
}
