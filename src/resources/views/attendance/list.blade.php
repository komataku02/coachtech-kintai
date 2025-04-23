@extends('layouts.app')
@section('page-css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="title">勤怠一覧</h2>

    @php
        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
    @endphp

    <table class="table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                @php
                    $date = \Carbon\Carbon::parse($attendance->work_date);
                    $clockIn = $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time) : null;
                    $clockOut = $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time) : null;

                    $breakMinutes = $attendance->breakTimes->sum(function ($break) {
                        return ($break->break_start && $break->break_end)
                            ? \Carbon\Carbon::parse($break->break_end)->diffInMinutes(\Carbon\Carbon::parse($break->break_start))
                            : 0;
                    });

                    $breakFormatted = $breakMinutes > 0 ? sprintf('%d:%02d', floor($breakMinutes / 60), $breakMinutes % 60) : '-';

                    $totalMinutes = ($clockIn && $clockOut) ? $clockOut->diffInMinutes($clockIn) - $breakMinutes : null;
                    $totalFormatted = $totalMinutes !== null ? sprintf('%d:%02d', floor($totalMinutes / 60), $totalMinutes % 60) : '-';
                @endphp
                <tr>
                    <td>{{ $date->format('Y年m月d日') }}（{{ $weekDays[$date->dayOfWeek] }}）</td>
                    <td>{{ $clockIn ? $clockIn->format('H:i') : '-' }}</td>
                    <td>{{ $clockOut ? $clockOut->format('H:i') : '-' }}</td>
                    <td>{{ $breakFormatted }}</td>
                    <td>{{ $totalFormatted }}</td>
                    <td>
                        <a href="{{ route('attendance.show', $attendance->id) }}" class="btn-link">詳細</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">勤怠情報がありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
