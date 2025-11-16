<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return ['status' => 'ok'];
});

Route::apiResource('students', StudentController::class);

Route::post('attendance/bulk', [AttendanceController::class, 'bulkRecord']);
Route::get('attendance/monthly', [AttendanceController::class, 'monthlyReport']);

Route::get('attendance/stats', [AttendanceController::class, 'stats']);
