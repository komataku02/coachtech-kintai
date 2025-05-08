@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="admin-attendance-detail">
    <h2 class="page-title">勤怠詳細（管理者）</h2>

    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}" class="attendance-update-form">
        @csrf
        @method('PUT')

        <div class="attendance-info-box">
            <p class="attendance-date"><strong>日付：</strong>{{ $attendance->work_date }}</p>

            <div class="form-group">
                <label for="clock_in_time" class="form-label">出勤時刻</label>
                <input type="time" name="clock_in_time" id="clock_in_time" class="form-input" value="{{ $attendance->clock_in_time }}">
            </div>

            <div class="form-group">
                <label for="clock_out_time" class="form-label">退勤時刻</label>
                <input type="time" name="clock_out_time" id="clock_out_time" class="form-input" value="{{ $attendance->clock_out_time }}">
            </div>

            <div class="form-group">
                <label for="note" class="form-label">備考</label>
                <textarea name="note" id="note" class="form-textarea" rows="3">{{ $attendance->note }}</textarea>
            </div>
        </div>

        <h3 class="sub-title">休憩時間</h3>
        <div class="breaks-section">
            @forelse ($attendance->breakTimes as $index => $break)
                <div class="break-row">
                    <div class="form-group">
                        <label class="form-label">休憩{{ $index + 1 }} 開始</label>
                        <input type="time" name="breaks[{{ $break->id }}][start]" class="form-input" value="{{ $break->break_start }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">休憩{{ $index + 1 }} 終了</label>
                        <input type="time" name="breaks[{{ $break->id }}][end]" class="form-input" value="{{ $break->break_end }}">
                    </div>
                </div>
            @empty
                <p class="no-break">休憩記録なし</p>
            @endforelse
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-approve">勤怠を更新する</button>
        </div>
    </form>

    <div class="back-link">
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-back">← 日別勤怠一覧に戻る</a>
    </div>
</div>
@endsection
