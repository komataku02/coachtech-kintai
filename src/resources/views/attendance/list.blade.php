@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/user/attendance-list.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="attendance-inner">
        <h2 class="title">勤怠一覧</h2>

        <div class="month-navigation">
            <a href="{{ route('attendance.list', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="btn-nav">← 前月</a>

            <form method="GET" action="{{ route('attendance.list') }}" class="form-month-select">
            <input type="month" name="month" value="{{ $month->format('Y-m') }}" onchange="this.form.submit()">
            </form>

            <a href="{{ route('attendance.list', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="btn-nav">翌月 →</a>
        </div>

        <table class="styled-table">
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
                        $workDate = \Carbon\Carbon::parse($attendance->work_date);
                        $dayOfWeek = ['日','月','火','水','木','金','土'][$workDate->dayOfWeek];
                        $in = $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time) : null;
                        $out = $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time) : null;

                        $breakMinutes = $attendance->breakTimes->sum(function ($break) {
                            return \Carbon\Carbon::parse($break->break_end)->diffInMinutes(\Carbon\Carbon::parse($break->break_start));
                        });

                        $breakFormatted = sprintf('%d:%02d', floor($breakMinutes / 60), $breakMinutes % 60);

                        $totalMinutes = ($in && $out) ? $in->diffInMinutes($out) - $breakMinutes : null;
                        $totalFormatted = $totalMinutes !== null ? sprintf('%d:%02d', floor($totalMinutes / 60), $totalMinutes % 60) : '-';
                    @endphp

                    <tr>
                        <td>{{ $workDate->format('m/d') }}（{{ $dayOfWeek }}）</td>
                        <td>{{ $in ? $in->format('H:i') : '-' }}</td>
                        <td>{{ $out ? $out->format('H:i') : '-' }}</td>
                        <td>{{ $breakMinutes > 0 ? $breakFormatted : '-' }}</td>
                        <td>{{ $totalFormatted }}</td>
                        <td><a href="{{ route('attendance.show', $attendance->id) }}" class="btn-link">詳細</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="no-data text-center">勤怠情報がありません。</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
