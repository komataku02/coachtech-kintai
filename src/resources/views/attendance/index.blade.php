@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="title">勤怠登録</h2>

    <div class="user-info">
        <p><strong></strong>{{ $attendance->status ?? '勤務外' }}</p>
        @php
            $now = \Carbon\Carbon::now();
            $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
        @endphp

        <div class="user-info">
            {{-- 年月日（曜日） --}}
            <p><strong></strong>{{ $now->format('Y年n月j日') }}（{{ $weekDays[$now->dayOfWeek] }}）</p>

            {{-- 時：分（2桁ゼロ埋め） --}}
            <p class="time-display"><strong></strong>{{ $now->format('H:i') }}</p>
        </div>
    </div>

    <div class="button-group">
        @if (!$attendance)
            {{-- 勤務外：出勤ボタンのみ --}}
            <form method="POST" action="{{ route('attendance.clockIn') }}">
                @csrf
                <button type="submit" class="btn blue">出勤</button>
            </form>

        @elseif ($attendance->status === '出勤')
            {{-- 出勤中：退勤・休憩入ボタン --}}
            <form method="POST" action="{{ route('attendance.breakIn') }}">
                @csrf
                <button type="submit" class="btn yellow">休憩入</button>
            </form>

            <form method="POST" action="{{ route('attendance.clockOut') }}">
                @csrf
                <button type="submit" class="btn red">退勤</button>
            </form>

        @elseif ($attendance->status === '休憩中')
            {{-- 休憩中：休憩戻ボタン --}}
            <form method="POST" action="{{ route('attendance.breakOut') }}">
                @csrf
                <button type="submit" class="btn green">休憩戻</button>
            </form>

        @elseif ($attendance->status === '退勤済')
            {{-- 退勤後：ボタンなし、メッセージ --}}
            <p class="thanks-message">お疲れさまでした。</p>
        @endif
    </div>
</div>
@endsection
