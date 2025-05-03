<?php

namespace App\Http\Controllers\Admin\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class DailyListController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        $attendances = Attendance::with('user','breakTimes')
            ->where('work_date', $date)
            ->orderBy('user_id')
            ->paginate(10);

        return view('admin.attendance.index', [
            'attendances' => $attendances,
            'date' => $date,
        ]);
    }
}
