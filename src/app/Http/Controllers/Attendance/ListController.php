<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class ListController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $targetMonth = $request->query('month', now()->format('Y-m'));

        try {
            $month = Carbon::createFromFormat('Y-m', $targetMonth)->startOfMonth();
        } catch (\Exception $e) {
            $month = now()->startOfMonth();
        }

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->orderBy('work_date', 'asc')
            ->get();

        return view('attendance.list', compact('attendances', 'month'));
    }
}
