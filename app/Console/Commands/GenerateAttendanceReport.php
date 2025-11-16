<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Illuminate\Console\Command;

class GenerateAttendanceReport extends Command
{
    // attendance:generate-report 2025-02 10
    protected $signature = 'attendance:generate-report {month} {class}';

    protected $description = 'Generate monthly attendance report for a given class (YYYY-MM, class)';

    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        parent::__construct();
        $this->attendanceService = $attendanceService;
    }

    public function handle(): int
    {
        $month = $this->argument('month'); // e.g. 2025-02
        $class = $this->argument('class'); // e.g. 10

        // Basic validation for month format
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $this->error('Month must be in YYYY-MM format, e.g. 2025-02');
            return Command::FAILURE;
        }

        $report = $this->attendanceService->getMonthlyReport($month, $class);

        if ($report->isEmpty()) {
            $this->warn("No attendance records found for class {$class} in {$month}.");
            return Command::SUCCESS;
        }

        // Totals
        $totals = [
            'present' => 0,
            'absent'  => 0,
            'late'    => 0,
        ];

        foreach ($report as $attendance) {
            if (isset($totals[$attendance->status])) {
                $totals[$attendance->status]++;
            }
        }

        $this->info("Attendance report for class {$class} ({$month})");
        $this->newLine();

        // Table of individual records
        $this->table(
            ['Student', 'Student ID', 'Date', 'Status', 'Note'],
            $report->map(function ($a) {
                return [
                    $a->student?->name ?? '-',
                    $a->student?->student_id ?? '-',
                    $a->date?->toDateString(),
                    ucfirst($a->status),
                    $a->note ?? '-',
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info('Totals:');
        foreach ($totals as $status => $count) {
            $this->line('  ' . ucfirst($status) . ': ' . $count);
        }

        return Command::SUCCESS;
    }
}
