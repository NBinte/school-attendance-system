<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::get('/health', fn() => ['status' => 'ok']);


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('students', StudentController::class);

    Route::post('attendance/bulk', [AttendanceController::class, 'bulkRecord']);
    Route::get('attendance/monthly', [AttendanceController::class, 'monthlyReport']);
    Route::get('attendance/stats', [AttendanceController::class, 'stats']);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});
