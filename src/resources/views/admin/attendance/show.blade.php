@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="page-title">勤怠詳細（管理者）</h2>

    <div class="record-box">
        <p><span class="label">日付：</span>{{ $attendance->work_date }}</p>
        <p><span class="label">出勤：</span>{{ $attendance->clock_in_time ?? '未登録' }}</p>
        <p><span class="label">退勤：</span>{{ $attendance->clock_out_time ?? '未登録' }}</p>
        <p><span class="label">ステータス：</span>{{ $attendance->status }}</p>
        <p><span class="label">備考：</span>{{ $attendance->note ?? 'なし' }}</p>
    </div>

    <div class="record-box">
        <h3 class="section-title">休憩時間</h3>
        <ul class="break-list">
            @forelse ($attendance->breakTimes as $break)
                <li>{{ $break->break_start }} ～ {{ $break->break_end }}</li>
            @empty
                <li>休憩記録なし</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
