<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $service;

    public function __construct(AttendanceService $service)
    {
        $this->service = $service;
    }

    // POST /api/attendance/bulk
    public function bulkRecord(Request $request)
    {
        $request->validate([
            'date'                    => ['required', 'date'],
            'class'                   => ['required', 'string'],
            'section'                 => ['nullable', 'string'],
            'attendance'              => ['required', 'array'],
            'attendance.*.student_id' => ['required', 'integer'],
            'attendance.*.status'     => ['required', 'in:present,absent,late'],
            'attendance.*.note'       => ['nullable', 'string'],
        ]);

        try {
            $this->service->recordBulk(
                date: $request->date,
                class: $request->class,
                section: $request->section,
                attendanceData: $request->attendance,
                recordedBy: 1
            );
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json(['message' => 'Attendance recorded successfully']);
    }
    // GET /api/attendance/monthly?month=2025-02&class=10
    public function monthlyReport(Request $request)
    {
        $request->validate([
            'month' => ['required', 'regex:/^\d{4}-\d{2}$/'], // YYYY-MM
            'class' => ['required', 'string'],
        ]);

        $data = $this->service->getMonthlyReport(
            month: $request->month,
            class: $request->class
        );

        return response()->json($data);
    }
}
