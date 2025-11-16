<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use App\Events\AttendanceRecorded;

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
}
