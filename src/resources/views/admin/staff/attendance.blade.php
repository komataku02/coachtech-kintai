@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">{{ $user->name }}さんの勤怠</h2>

    {{-- 月ナビゲーション --}}
    @php
        $currentMonth = \Carbon\Carbon::parse($month);
        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
    @endphp

    <div class="month-navigation">
        <a href="{{ route('admin.staff.attendance', ['id' => $user->id, 'month' => $prevMonth]) }}" class="btn-nav">← 前月</a>

        {{-- カレンダー入力 --}}
        <form method="GET" action="{{ route('admin.staff.attendance', ['id' => $user->id]) }}" class="inline-form">
            <input type="month" name="month" value="{{ $currentMonth->format('Y-m') }}">
            <button type="submit" class="btn-submit">移動</button>
        </form>

        <a href="{{ route('admin.staff.attendance', ['id' => $user->id, 'month' => $nextMonth]) }}" class="btn-nav">翌月 →</a>
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
            @foreach ($attendances as $attendance)
            @php
                $clockIn = $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '-';
                $clockOut = $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '-';
                $breakMinutes = $attendance->breakTimes->sum(function ($break) {
                return ($break->break_start && $break->break_end)
                    ? \Carbon\Carbon::parse($break->break_end)->diffInMinutes(\Carbon\Carbon::parse($break->break_start))
                    : 0;
            });
                $breakFormatted = sprintf('%d:%02d', floor($breakMinutes / 60), $breakMinutes % 60);
                $totalMinutes = ($attendance->clock_in_time && $attendance->clock_out_time)
                ? \Carbon\Carbon::parse($attendance->clock_out_time)->diffInMinutes(\Carbon\Carbon::parse($attendance->clock_in_time)) - $breakMinutes
                : null;
                $totalFormatted = $totalMinutes !== null ? sprintf('%d:%02d', floor($totalMinutes / 60), $totalMinutes % 60) : '-';

            // 日付と曜日（日本語）
                $workDate = \Carbon\Carbon::parse($attendance->work_date);
                $weekday = ['日', '月', '火', '水', '木', '金', '土'][$workDate->dayOfWeek];
            @endphp
            <tr>
                <td>{{ $workDate->format('m/d') }}（{{ $weekday }}）</td>
                <td>{{ $clockIn }}</td>
                <td>{{ $clockOut }}</td>
                <td>{{ $breakFormatted }}</td>
                <td>{{ $totalFormatted }}</td>
                <td><a href="{{ route('admin.attendance.detail', $attendance->id) }}" class="btn-link">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="csv-download right">
        <a href="{{ route('admin.staff.attendance.csv', ['id' => $user->id, 'month' => $currentMonth->format('Y-m')]) }}" class="btn-csv">CSV出力</a>
    </div>

    <div class="back-link">
        <a href="{{ route('admin.staff.list') }}">← スタッフ一覧に戻る</a>
    </div>
</div>
@endsection
