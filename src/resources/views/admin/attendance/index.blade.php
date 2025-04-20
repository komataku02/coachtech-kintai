@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">日別勤怠一覧</h2>
    <div class="date-navigation">
    <a href="{{ route('admin.attendance.index', ['date' => \Carbon\Carbon::parse($date)->copy()->subDay()->format('Y-m-d')]) }}" class="btn-link">← 前日</a>
    <span class="current-date">{{ \Carbon\Carbon::parse($date)->format('Y年m月d日') }}</span>
    <a href="{{ route('admin.attendance.index', ['date' => \Carbon\Carbon::parse($date)->copy()->addDay()->format('Y-m-d')]) }}" class="btn-link">翌日 →</a>
    </div>

    @if ($attendances->isEmpty())
        <p class="no-data">勤怠情報がありません。</p>
    @else
        <table class="styled-table">
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

    // 休憩時間合計（分）
    $totalBreak = $attendance->breakTimes->sum(function ($break) {
        return \Carbon\Carbon::parse($break->break_start)->diffInMinutes(\Carbon\Carbon::parse($break->break_end));
    });

    // 合計勤務時間（分）
    $totalWorkMinutes = ($in && $out) ? $in->diffInMinutes($out) - $totalBreak : null;

    // 時刻表示（H:i形式）
    $inTime = $in ? $in->format('H:i') : '-';
    $outTime = $out ? $out->format('H:i') : '-';

    // 休憩時間（H:i形式で時間:分）
    $breakHours = floor($totalBreak / 60);
    $breakMinutes = str_pad($totalBreak % 60, 2, '0', STR_PAD_LEFT);
    $breakFormatted = sprintf('%d:%s', $breakHours, $breakMinutes);

    // 勤務時間（H:i形式）
    if ($totalWorkMinutes !== null && $totalWorkMinutes >= 0) {
        $workHours = floor($totalWorkMinutes / 60);
        $workMinutes = str_pad($totalWorkMinutes % 60, 2, '0', STR_PAD_LEFT);
        $workFormatted = sprintf('%d:%s', $workHours, $workMinutes);
    } else {
        $workFormatted = '-';
    }
    @endphp
            <tr>
                <td>{{ $attendance->user->name }}</td>
    <td>{{ $inTime }}</td>
    <td>{{ $outTime }}</td>
    <td>{{ $attendance->breakTimes->isNotEmpty() ? $breakFormatted : '-' }}</td>
    <td>{{ $workFormatted }}</td>
    <td>
        <a href="{{ route('admin.attendance.detail', $attendance->id) }}" class="btn-link">詳細</a>
    </td>
            </tr>
        @endforeach
            </tbody>
        </table>
    @endif
    <div class="pagination">
            {{ $attendances->links() }}
        </div>
</div>
@endsection
