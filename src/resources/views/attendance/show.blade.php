@extends('layouts.app')
@section('page-css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="title">勤怠詳細</h2>

    <div class="detail-box">
        <p><strong>日付：</strong>{{ $attendance->work_date }}</p>
        <p><strong>出勤：</strong>{{ $attendance->clock_in_time ?? '未登録' }}</p>
        <p><strong>退勤：</strong>{{ $attendance->clock_out_time ?? '未登録' }}</p>
        <p><strong>ステータス：</strong>{{ $attendance->status }}</p>
        <p><strong>備考：</strong>{{ $attendance->note ?? 'なし' }}</p>
    </div>

    <div class="breaks-section">
        <h3>休憩時間</h3>
        <ul>
            @forelse ($attendance->breakTimes as $break)
                <li>{{ $break->break_start }} ～ {{ $break->break_end }}</li>
            @empty
                <li>休憩記録なし</li>
            @endforelse
        </ul>
    </div>

    <div class="back-link">
        <a href="{{ route('attendance.list') }}">← 勤怠一覧に戻る</a>
    </div>
</div>
@endsection
