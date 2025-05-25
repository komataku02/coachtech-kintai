<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;;
use App\Models\Application;


class DetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);

        if ($attendance->user_id != Auth::id()) {
            return redirect()->route('attendance.list')->with('error', '他人の勤怠にはアクセスできません。');
        }

        $alreadyApplied = Application::where('user_id', Auth::id())
            ->where('attendance_id', $attendance->id)
            ->exists();

        return view('attendance.show', [
            'attendance' => $attendance,
            'alreadyApplied' => $alreadyApplied,
        ]);
    }

}
