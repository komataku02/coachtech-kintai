@extends('layouts.app')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="container">
    {{-- 成功メッセージ --}}
    @if (session('message'))
        <div class="alert-success">
            {{ session('message') }}
        </div>
    @endif

    {{-- エラーメッセージ --}}
    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="title">勤怠詳細（修正）</h2>

    <form method="POST" action="{{ route('application.store') }}">
        @csrf
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

        <div class="form-group">
            <label>名前</label>
            <p>{{ $attendance->user->name ?? Auth::user()->name }}</p>
        </div>

        <div class="form-group">
            <label>日付</label>
            <p>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年n月j日（'.$weekDay = ['日', '月', '火', '水', '木', '金', '土'][\Carbon\Carbon::parse($attendance->work_date)->dayOfWeek].'）') }}</p>
        </div>

        <div class="form-group">
            <label for="clock_in_time">出勤・退勤</label>
            <div class="time-range">
                <input type="time" name="clock_in_time" value="{{ old('clock_in_time', optional($attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time) : null)->format('H:i')) }}">
                ～
                <input type="time" name="clock_out_time" value="{{ old('clock_out_time', optional($attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time) : null)->format('H:i')) }}">
            </div>
            @error('clock_in_time')
                <p class="error-text">{{ $message }}</p>
            @enderror
            @error('clock_out_time')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>休憩</label>
            @forelse ($attendance->breakTimes as $break)
                <div class="break-pair">
                    <input type="time" name="breaks[{{ $break->id }}][start]"
                           value="{{ old("breaks.{$break->id}.start", \Carbon\Carbon::parse($break->break_start)->format('H:i')) }}">
                    ～
                    <input type="time" name="breaks[{{ $break->id }}][end]"
                           value="{{ $break->break_end ? old("breaks.{$break->id}.end", \Carbon\Carbon::parse($break->break_end)->format('H:i')) : '' }}">
                </div>
            @empty
                <p class="no-break">記録された休憩はありません</p>
            @endforelse
        </div>

        <div class="form-group">
            <label for="note">備考（任意）</label>
            <textarea name="note" id="note" rows="3">{{ old('note', $attendance->note) }}</textarea>
            @error('note')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="request_reason">修正理由（必須）</label>
            <textarea name="request_reason" id="request_reason" rows="3">{{ old('request_reason') }}</textarea>
            @error('request_reason')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-approve">修正申請を送信</button>
        </div>
    </form>

    <div class="back-link">
        <a href="{{ route('attendance.list') }}">← 勤怠一覧に戻る</a>
    </div>
</div>
@endsection
