@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">勤怠詳細（管理者）</h2>

    {{-- フォーム開始 --}}
    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}">
        @csrf
        @method('PUT')

        <div class="detail-box">
            <p><strong>日付：</strong>{{ $attendance->work_date }}</p>

            <label for="clock_in_time">出勤時刻</label>
            <input type="time" name="clock_in_time" id="clock_in_time" value="{{ $attendance->clock_in_time }}">

            <label for="clock_out_time">退勤時刻</label>
            <input type="time" name="clock_out_time" id="clock_out_time" value="{{ $attendance->clock_out_time }}">

            <label for="note">備考</label>
            <textarea name="note" id="note" rows="3">{{ $attendance->note }}</textarea>
        </div>

        <h3 class="sub-title">休憩時間</h3>
        <div class="breaks-section">
            @forelse ($attendance->breakTimes as $index => $break)
                <div class="break-row">
                    <label>休憩{{ $index + 1 }}開始</label>
                    <input type="time" name="breaks[{{ $break->id }}][start]" value="{{ $break->break_start }}">

                    <label>休憩{{ $index + 1 }}終了</label>
                    <input type="time" name="breaks[{{ $break->id }}][end]" value="{{ $break->break_end }}">
                </div>
            @empty
                <p class="no-break">休憩記録なし</p>
            @endforelse
        </div>

        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-approve">勤怠を更新する</button>
        </div>
    </form>

    {{-- 戻るリンク --}}
    <div class="back-link mt-4">
        <a href="{{ route('admin.attendance.index') }}">← 日別勤怠一覧に戻る</a>
    </div>
</div>
@endsection
