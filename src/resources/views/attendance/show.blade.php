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

    <div class="breaks-section">
        <h3 class="subtitle">休憩時間</h3>
        <ul class="breaks-list">
            @forelse ($attendance->breakTimes as $break)
                <div class="break-pair">
                    <input type="time" name="breaks[{{ $break->id }}][start]" value="{{ old("breaks.{$break->id}.start", \Carbon\Carbon::parse($break->break_start)->format('H:i')) }}">
                    ～
                    <input type="time" name="breaks[{{ $break->id }}][end]" value="{{ $break->break_end ? old("breaks.{$break->id}.end", \Carbon\Carbon::parse($break->break_end)->format('H:i')) : '' }}">
                </div>
            @empty
                <p class="no-break">記録された休憩はありません</p>
            @endforelse
        </div>

        <div class="form-group">
            <label for="note">備考（必須）</label>
            <textarea name="note" id="note" rows="3">{{ old('note', $attendance->note) }}</textarea>
            @error('note')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-approve">修正申請を送信</button>
        </div>
    </form>

    {{-- 修正申請ボタン --}}
    <div class="apply-link">
        @if ($alreadyApplied)
            <p class="apply-alert">※この勤怠にはすでに申請済みです。</p>
        @else
            <a href="{{ route('application.create', ['attendance_id' => $attendance->id]) }}"
               class="btn btn-apply">修正申請する</a>
        @endif
    </div>

    <div class="back-link">
        <a href="{{ route('attendance.list') }}" class="btn btn-back">← 勤怠一覧に戻る</a>
    </div>
</div>
@endsection
