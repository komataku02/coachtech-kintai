@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/user/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="user-info">
        <p class="status-label">{{ $attendance->status ?? '勤務外' }}</p>

        <p class="current-date" id="current-date">--年--月--日（--）</p>
        <p class="time-display" id="current-time">--:--</p>
    </div>

    <div class="button-group">
        @if (!$attendance)
            <form method="POST" action="{{ route('attendance.clockIn') }}">
                @csrf
                <button type="submit" class="btn-clock clock-in">出勤</button>
            </form>
        @elseif ($attendance->status === '出勤中')
            <form method="POST" action="{{ route('attendance.clockOut') }}">
                @csrf
                <button type="submit" class="btn-clock clock-out">退勤</button>
            </form>
            <form method="POST" action="{{ route('attendance.breakIn') }}">
                @csrf
                <button type="submit" class="btn-clock break-in">休憩入</button>
            </form>
        @elseif ($attendance->status === '休憩中')
            <form method="POST" action="{{ route('attendance.breakOut') }}">
                @csrf
                <button type="submit" class="btn-clock break-out">休憩戻</button>
            </form>
        @elseif ($attendance->status === '退勤済')
            <p class="thanks-message">お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateDateTime() {
        const now = new Date();
        const weekDays = ['日', '月', '火', '水', '木', '金', '土'];

        const year = now.getFullYear();
        const month = now.getMonth() + 1;
        const date = now.getDate();
        const day = weekDays[now.getDay()];
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        document.getElementById('current-date').textContent = `${year}年${month}月${date}日（${day}）`;
        document.getElementById('current-time').textContent = `${hours}:${minutes}`;
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateDateTime();
        setInterval(updateDateTime, 1000);
    });
</script>
@endsection
