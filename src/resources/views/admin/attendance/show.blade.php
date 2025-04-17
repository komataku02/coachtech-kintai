@extends('layouts.app')@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection


@section('content')
<div class="container">
    <h2 class="page-title">勤怠詳細（管理者）</h2>

    <div class="detail-box">
        <p><strong>日付：</strong>{{ $attendance->work_date }}</p>
        <p><strong>出勤：</strong>{{ $attendance->clock_in_time ?? '未登録' }}</p>
        <p><strong>退勤：</strong>{{ $attendance->clock_out_time ?? '未登録' }}</p>
        <p><strong>ステータス：</strong>{{ $attendance->status }}</p>
        <p><strong>備考：</strong>{{ $attendance->note ?? 'なし' }}</p>
    </div>

    <h3 class="sub-title">休憩時間</h3>
    @if ($attendance->breakTimes->isEmpty())
        <p class="no-break">休憩記録なし</p>
    @else
        <ul class="break-list">
            @foreach ($attendance->breakTimes as $break)
                <li>{{ $break->break_start }} ～ {{ $break->break_end }}</li>
            @endforeach
        </ul>
    @endif

    <div class="back-link">
        <a href="{{ route('admin.attendance.index') }}">← 日別勤怠一覧に戻る</a>
    </div>
</div>
@endsection

