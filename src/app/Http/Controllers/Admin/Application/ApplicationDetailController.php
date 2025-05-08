<?php

namespace App\Http\Controllers\Admin\Application;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationDetailController extends Controller
{
    public function show($id)
    {
        $application = Application::with(['user', 'attendance.breakTimes'])->findOrFail($id);

        $clockIn = $application->attendance->clock_in_time
            ? \Carbon\Carbon::parse($application->attendance->clock_in_time)->format('H:i')
            : '--:--';

        $clockOut = $application->attendance->clock_out_time
            ? \Carbon\Carbon::parse($application->attendance->clock_out_time)->format('H:i')
            : '--:--';

        return view('admin.application.show', compact('application', 'clockIn', 'clockOut'));
    }

    public function approve(Request $request, $id)
    {
        $application = Application::with('attendance')->findOrFail($id);

        if ($application->status === 'approved') {
            return redirect()->route('admin.application.detail', $id)
                ->with('message', 'この申請はすでに承認されています。');
        }

        $attendance = $application->attendance;

        if ($application->request_clock_in !== null) {
            $attendance->clock_in_time = $application->request_clock_in;
        }
        if ($application->request_clock_out !== null) {
            $attendance->clock_out_time = $application->request_clock_out;
        }
        if ($application->note !== null) {
            $attendance->note = $application->note;
        }
        $attendance->save();

        $attendance->breakTimes()->delete();

        $breaks = json_decode($application->request_breaks, true);
        if (is_array($breaks)) {
            usort($breaks, fn($a, $b) => strtotime($a['start']) <=> strtotime($b['start']));

            $validBreaks = [];

            foreach ($breaks as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    $start = strtotime($break['start']);
                    $end = strtotime($break['end']);

                    if ($end <= $start) {
                        continue;
                    }

                    $isOverlapping = false;
                    foreach ($validBreaks as $valid) {
                        $s = strtotime($valid['start']);
                        $e = strtotime($valid['end']);
                        if ($start < $e && $end > $s) {
                            $isOverlapping = true;
                            break;
                        }
                    }

                    if (!$isOverlapping) {
                        $attendance->breakTimes()->create([
                            'break_start' => $break['start'],
                            'break_end' => $break['end'],
                        ]);
                        $validBreaks[] = $break;
                    }
                }
            }
        }

        $application->status = 'approved';
        $application->approved_at = now();
        $application->save();

        return redirect()->route('admin.application.list')
            ->with('message', '申請を承認し、勤怠情報を更新しました。');
    }
}
