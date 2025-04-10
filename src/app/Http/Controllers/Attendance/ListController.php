<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class ListController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 当月の勤怠情報を取得
        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('work_date', now()->month)
            ->orderBy('work_date', 'desc')
            ->get();

        return view('attendance.list', compact('attendances'));
    }
}
