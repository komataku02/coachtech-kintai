@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">勤怠詳細</h2>

    <p><strong>日付：</strong>{{ $attendance->work_date }}</p>
    <p><strong>出勤：</strong>{{ $attendance->clock_in_time ?? '未登録' }}</p>
    <p><strong>退勤：</strong>{{ $attendance->clock_out_time ?? '未登録' }}</p>
    <p><strong>ステータス：</strong>{{ $attendance->status }}</p>
    <p><strong>備考：</strong>{{ $attendance->note ?? 'なし' }}</p>

    <h3 class="mt-4 font-semibold">休憩時間</h3>
    <ul>
        @forelse ($attendance->breakTimes as $break)
            <li>{{ $break->break_start }} ～ {{ $break->break_end }}</li>
        @empty
            <li>休憩記録なし</li>
        @endforelse
    </ul>
</div>
@endsection
