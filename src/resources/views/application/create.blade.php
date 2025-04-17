@extends('layouts.app')
@section('page-css')
<link rel="stylesheet" href="{{ asset('css/application.css') }}">
@endsection


@section('content')
<div class="container">
    <h2 class="title">修正申請フォーム</h2>

    <div class="box">
        <p><strong>日付：</strong>{{ $attendance->work_date }}</p>
        <p><strong>出勤：</strong>{{ $attendance->clock_in_time ?? '未記録' }}</p>
        <p><strong>退勤：</strong>{{ $attendance->clock_out_time ?? '未記録' }}</p>
        <p><strong>ステータス：</strong>{{ $attendance->status }}</p>
    </div>

    @if ($alreadyApplied)
        <p class="alert-error">この勤怠には既に申請を送信済みです。</p>
    @else
        <form method="POST" action="{{ route('application.store') }}">
            @csrf
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            <div class="form-group">
                <label for="request_reason">申請理由</label>
                <textarea name="request_reason" id="request_reason" rows="4">{{ old('request_reason') }}</textarea>
                @error('request_reason')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">申請する</button>
        </form>
    @endif
</div>
@endsection
