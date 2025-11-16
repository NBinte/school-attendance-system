<?php

namespace App\Listeners;

use App\Events\AttendanceRecorded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendAttendanceNotification
{
    /**
     * Handle the event.
     */
    public function handle(AttendanceRecorded $event): void
    {
        $attendance = $event->attendance;
        $student    = $attendance->student;

        Log::info('Attendance recorded', [
            'student_id'   => $student?->id,
            'student_name' => $student?->name,
            'status'       => $attendance->status,
            'date'         => $attendance->date?->toDateString(),
        ]);

        // In a real app, we might:
        // - Send email/SMS to parents
        // - Dispatch a notification
        // For this test, logging is enough to prove the listener runs.
    }
}
