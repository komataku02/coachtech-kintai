@extends('layouts.app')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="container">
    {{-- エラーメッセージの全体表示 --}}
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

    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="clock_in_time">出勤時刻</label>
            <input type="time" name="clock_in_time" id="clock_in_time" value="{{ old('clock_in_time', $attendance->clock_in_time) }}">
            @error('clock_in_time')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="clock_out_time">退勤時刻</label>
            <input type="time" name="clock_out_time" id="clock_out_time" value="{{ old('clock_out_time', $attendance->clock_out_time) }}">
            @error('clock_out_time')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 休憩入力（複数対応） --}}
        <div class="form-group">
            <label>休憩時間</label>
            @forelse ($attendance->breakTimes as $i => $break)
                <div class="break-pair">
                    <input type="time" name="breaks[{{ $i }}][break_start]" value="{{ old("breaks.$i.break_start", $break->break_start) }}">
                    ～
                    <input type="time" name="breaks[{{ $i }}][break_end]" value="{{ old("breaks.$i.break_end", $break->break_end) }}">
                </div>
            @empty
                <p class="no-break">休憩記録なし</p>
            @endforelse

            {{-- エラーメッセージ（例：1個目） --}}
            @error('breaks.0.break_start')
                <p class="error-text">{{ $message }}</p>
            @enderror
            @error('breaks.0.break_end')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="note">備考</label>
            <textarea name="note" id="note" rows="3">{{ old('note', $attendance->note) }}</textarea>
            @error('note')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-approve">修正を保存する</button>
        </div>
    </form>

    <div class="back-link">
        <a href="{{ route('admin.attendance.index') }}">← 日別勤怠一覧に戻る</a>
    </div>
</div>
@endsection
