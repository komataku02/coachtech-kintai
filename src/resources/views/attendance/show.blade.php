@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    <div class="attendance-user">
        <p><strong>名前</strong>{{ Auth::user()->name }}</p>
        </div>

    <div class="attendance-date">
        <p><strong>日付</strong>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年n月j日') }}</p>
    </div>

    <div class="attendance-times">
        <p><strong>出勤</strong>{{ $attendance->clock_in_time ?? '--:--' }}</p>
        <p><strong>退勤</strong>{{ $attendance->clock_out_time ?? '--:--' }}</p>
    </div>

    <div class="breaks-section">
        <h3 class="subtitle">休憩時間</h3>
        <ul class="breaks-list">
            @forelse ($attendance->breakTimes as $break)
                <li class="break-pair">
                    <span>{{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }}</span>
                    ～ 
                    <span>{{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}</span>
                </li>
            @empty
                <li class="no-break">記録された休憩はありません</li>
            @endforelse
        </ul>
    </div>

    <div class="note-section">
        <h3 class="subtitle">備考</h3>
        <p class="note-text">{{ $attendance->note ?? '―' }}</p>
    </div>

    <div class="apply-link">
        @if ($alreadyApplied)
            <p class="apply-alert">※この勤怠にはすでに申請済みです。</p>
        @else
            <a href="{{ route('application.create', ['attendance_id' => $attendance->id]) }}" class="btn btn-apply">修正申請する</a>
        @endif
    </div>

    <div class="back-link">
        <a href="{{ route('attendance.list') }}" class="btn btn-back">← 勤怠一覧に戻る</a>
    </div>
</div>
@endsection

