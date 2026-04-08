<?php
use App\Http\Controllers\Api\AttendanceController;
use Illuminate\Support\Facades\Route;

// Login route for employees to get API token
Route::post('login', [AttendanceController::class, 'login']);

// Routes protected for authenticated employees only
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/today', [AttendanceController::class, 'todayAttendance']);
});