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

        // クエリパラメータから年月を取得（例: 2025-03）
        $targetMonth = $request->query('month', now()->format('Y-m'));

        // Carbonに変換（フォールバックも含む）
        try {
            $month = \Carbon\Carbon::createFromFormat('Y-m', $targetMonth)->startOfMonth();
        } catch (\Exception $e) {
            $month = now()->startOfMonth();
        }

        // 対象月の勤怠を取得
        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->orderBy('work_date', 'desc')
            ->get();

        return view('attendance.list', [
            'attendances' => $attendances,
            'month' => $month,
        ]);
    }
}
