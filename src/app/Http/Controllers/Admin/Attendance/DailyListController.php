<?php

namespace App\Http\Controllers\Admin\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class DailyListController extends Controller
{
    public function index()
    {
        // 日付でグループ化して勤怠を取得
        $attendances = Attendance::orderBy('work_date', 'desc')->paginate(20);

        return view('admin.attendance.index', compact('attendances'));
    }
}
