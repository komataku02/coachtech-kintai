@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin/admin-attendance.css') }}">
@endsection

@section('content')
<div class="admin-attendance-container">
    <h2 class="page-title">
        {{ \Carbon\Carbon::parse($date)->format('Y年n月j日') }}の勤怠
    </h2>

    <div class="date-navigation">
        <a href="{{ route('admin.attendance.list', ['date' => \Carbon\Carbon::parse($date)->copy()->subDay()->format('Y-m-d')]) }}" class="btn-nav btn-prev">← 前日</a>

        <form method="GET" action="{{ route('admin.attendance.list') }}" class="date-form">
            <input type="date" name="date" class="date-input" value="{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}" onchange="this.form.submit()">
        </form>

        <a href="{{ route('admin.attendance.list', ['date' => \Carbon\Carbon::parse($date)->copy()->addDay()->format('Y-m-d')]) }}" class="btn-nav btn-next">翌日 →</a>
    </div>

    @if ($attendances->isEmpty())
        <p class="no-data">勤怠情報がありません。</p>
    @else
        <table class="attendance-table styled-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩時間</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                    @php
                        $in = $attendance->clock_in_time ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->clock_in_time) : null;
                        $out = $attendance->clock_out_time ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->clock_out_time) : null;

                        $totalBreak = $attendance->breakTimes->sum(function ($break) {
                            return \Carbon\Carbon::parse($break->break_start)->diffInMinutes(\Carbon\Carbon::parse($break->break_end));
                        });

                        $totalWorkMinutes = ($in && $out) ? $in->diffInMinutes($out) - $totalBreak : null;

                        $inTime = $in ? $in->format('H:i') : '-';
                        $outTime = $out ? $out->format('H:i') : '-';

                        $breakFormatted = sprintf('%d:%02d', intdiv($totalBreak, 60), $totalBreak % 60);
                        $workFormatted = $totalWorkMinutes !== null && $totalWorkMinutes >= 0
                            ? sprintf('%d:%02d', intdiv($totalWorkMinutes, 60), $totalWorkMinutes % 60)
                            : '-';
                    @endphp
                    <tr>
                        <td>{{ optional($attendance->user)->name ?? '-' }}</td>
                        <td>{{ $inTime }}</td>
                        <td>{{ $outTime }}</td>
                        <td>{{ $attendance->breakTimes->isNotEmpty() ? $breakFormatted : '-' }}</td>
                        <td>{{ $workFormatted }}</td>
                        <td>
                            <a href="{{ route('attendance.show', $attendance->id) }}" class="btn-detail">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="pagination-wrapper">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
