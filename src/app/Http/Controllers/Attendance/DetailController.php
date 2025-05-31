<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;


class DetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with(['breakTimes', 'application'])->findOrFail($id);

        if ($attendance->user_id != Auth::id()) {
            return redirect()->route('attendance.list')->with('error', '他人の勤怠にはアクセスできません。');
        }

        $application = $attendance->application;
        $alreadyApplied = !is_null($application);

        return view('attendance.show', [
            'attendance' => $attendance,
            'alreadyApplied' => $alreadyApplied,
            'application' => $application,
        ]);
    }
}
