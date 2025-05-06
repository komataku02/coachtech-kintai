@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="title">勤怠一覧</h2>

    {{-- 月ナビゲーション --}}
    <div class="month-nav">
        <a href="{{ route('attendance.list', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="btn-nav">← 前月</a>

        <form method="GET" action="{{ route('attendance.list') }}" class="inline-form">
            <input type="month" name="month" value="{{ $month->format('Y-m') }}" onchange="this.form.submit()">
        </form>

        <a href="{{ route('attendance.list', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="btn-nav">翌月 →</a>
    </div>

    {{-- 勤怠テーブル --}}
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
                <tr>
                    {{-- 月/日（曜日）形式に変換 --}}
                    <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d') }}（{{ ['日','月','火','水','木','金','土'][\Carbon\Carbon::parse($attendance->work_date)->dayOfWeek] }}）</td>
                    <td>{{ $attendance->clock_in_time ?? '-' }}</td>
                    <td>{{ $attendance->clock_out_time ?? '-' }}</td>
                    <td>
                        {{ $attendance->breakTimes->sum(function ($break) {
                            return \Carbon\Carbon::parse($break->break_end)->diffInMinutes(\Carbon\Carbon::parse($break->break_start));
                        }) ? gmdate('H:i', $attendance->breakTimes->sum(function ($break) {
                            return \Carbon\Carbon::parse($break->break_end)->diffInSeconds(\Carbon\Carbon::parse($break->break_start));
                        })) : '-' }}
                    </td>
                    <td>
                        @if ($attendance->clock_in_time && $attendance->clock_out_time)
                            {{ \Carbon\Carbon::parse($attendance->clock_out_time)->diff(\Carbon\Carbon::parse($attendance->clock_in_time))->format('%H:%I') }}
                        @else
                            -
                        @endif
                    </td>
                    <td><a href="{{ route('attendance.show', $attendance->id) }}" class="btn-link">詳細</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">勤怠情報がありません。</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection