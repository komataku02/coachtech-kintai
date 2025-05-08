@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/application.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="title">修正申請フォーム</h2>

    <div class="box application-summary">
        <p><strong>日付：</strong>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年m月d日') }}</p>
        <p><strong>出勤：</strong>{{ $attendance->clock_in_time ?? '未記録' }}</p>
        <p><strong>退勤：</strong>{{ $attendance->clock_out_time ?? '未記録' }}</p>
        <p><strong>ステータス：</strong>{{ $attendance->status }}</p>
    </div>

    @if ($alreadyApplied)
        <p class="alert-error">この勤怠には既に申請を送信済みです。</p>
    @else
        <form method="POST" action="{{ route('application.store') }}" class="form-wrapper">
            @csrf
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            <div class="form-group">
                <label for="note" class="form-label">備考（必須）</label>
                <textarea name="note" id="note" rows="4" class="form-textarea">{{ old('note', $attendance->note) }}</textarea>
                @error('note')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-submit">申請する</button>
            </div>
        </form>
    @endif

    <div class="back-link">
        <a href="{{ route('attendance.show', ['id' => $attendance->id]) }}" class="btn btn-back">← 勤怠詳細に戻る</a>
    </div>
</div>
@endsection
