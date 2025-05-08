@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <h2 class="title">勤怠登録</h2>

    <div class="user-info">
        <p class="status-label">{{ $attendance->status ?? '勤務外' }}</p>

        @php
            $now = \Carbon\Carbon::now();
            $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
        @endphp

        <p class="current-date">{{ $now->format('Y年n月j日') }}（{{ $weekDays[$now->dayOfWeek] }}）</p>
        <p class="time-display">{{ $now->format('H:i') }}</p>
    </div>

    <div class="button-group">
        @if (!$attendance)
            <form method="POST" action="{{ route('attendance.clockIn') }}">
                @csrf
                <button type="submit" class="btn-clock clock-in">出勤</button>
            </form>

        @elseif ($attendance->status === '出勤')
            <form method="POST" action="{{ route('attendance.breakIn') }}">
                @csrf
                <button type="submit" class="btn-clock break-in">休憩入</button>
            </form>

            <form method="POST" action="{{ route('attendance.clockOut') }}">
                @csrf
                <button type="submit" class="btn-clock clock-out">退勤</button>
            </form>

        @elseif ($attendance->status === '休憩中')
            <form method="POST" action="{{ route('attendance.breakOut') }}">
                @csrf
                <button type="submit" class="btn-clock break-out">休憩戻</button>
            </form>

        @elseif ($attendance->status === '退勤済')
            <p class="thanks-message">お疲れさまでした。</p>
        @endif
    </div>
</div>
@endsection
