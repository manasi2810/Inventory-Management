<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $employee = User::where('email', $request->email)->first();
        
        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $employee->createToken('employee-token')->plainTextToken;

        return response()->json(['token' => $token, 'employee' => $employee]);
    }

      public function checkIn(Request $request)
    {
        $user = $request->user(); // Authenticated employee
        $today = Carbon::today()->toDateString();

        // Prevent multiple check-ins
        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $user->id, 'date' => $today],
            ['check_in' => Carbon::now(), 'status' => 'Present']
        );

        if(!$attendance->wasRecentlyCreated) {
            return response()->json(['message' => 'Already checked in', 'data' => $attendance]);
        }

        return response()->json(['message' => 'Check-in successful', 'data' => $attendance]);
    }

     // Employee Check-out
   public function checkOut(Request $request)
{
    // 1️⃣ Get the authenticated employee from token
    $user = $request->user();

    // 2️⃣ Get today's attendance
    $today = now()->toDateString();
    $attendance = Attendance::where('employee_id', $user->id)
                            ->where('date', $today)
                            ->first();

    // 3️⃣ Check if employee has checked in today
    if (!$attendance) {
        return response()->json([
            'message' => 'You have not checked in today'
        ], 400);
    }

    // 4️⃣ Check if already checked out
    if ($attendance->check_out) {
        return response()->json([
            'message' => 'Already checked out today'
        ], 400);
    }

    // 5️⃣ Update checkout time
    $attendance->check_out = now();

    // 6️⃣ Optional: calculate working hours
    $attendance->working_hours = $attendance->check_in
        ? round((strtotime($attendance->check_out) - strtotime($attendance->check_in)) / 3600, 2)
        : null;

    $attendance->save();

    // 7️⃣ Return success
    return response()->json([
        'message' => 'Checked out successfully',
        'data' => $attendance
    ]);
}
    // Get today's attendance
    public function todayAttendance(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('employee_id', $user->id)
                                ->where('date', $today)
                                ->first();

        return response()->json(['data' => $attendance]);
    }
}