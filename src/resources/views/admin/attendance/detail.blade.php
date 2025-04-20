@extends('layouts.app')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">勤怠詳細（管理者）</h2>

    {{-- 成功メッセージ --}}
    @if (session('message'))
        <div class="alert-success">{{ session('message') }}</div>
    @endif

    {{-- バリデーションエラー --}}
    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 編集フォーム --}}
    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}">
        @csrf
        @method('PUT')

        <table class="detail-table">
            <tr>
                <th>日付</th>
                <td>{{ $attendance->work_date }}</td>
            </tr>
            <tr>
                <th>出勤</th>
                <td>
                    <input type="time" name="clock_in_time" value="{{ old('clock_in_time', $attendance->clock_in_time) }}">
                </td>
            </tr>
            <tr>
                <th>退勤</th>
                <td>
                    <input type="time" name="clock_out_time" value="{{ old('clock_out_time', $attendance->clock_out_time) }}">
                </td>
            </tr>

            {{-- 休憩時間 --}}
            @forelse ($attendance->breakTimes as $i => $break)
                <tr>
                    <th>休憩{{ $i + 1 }}</th>
                    <td>
                        <input type="time" name="break_start_times[{{ $i }}]" value="{{ old("break_start_times.$i", $break->break_start) }}">
                        ～
                        <input type="time" name="break_end_times[{{ $i }}]" value="{{ old("break_end_times.$i", $break->break_end) }}">
                    </td>
                </tr>
            @empty
                <tr>
                    <th>休憩1</th>
                    <td>
                        <input type="time" name="break_start_times[0]"> ～ <input type="time" name="break_end_times[0]">
                    </td>
                </tr>
            @endforelse

            {{-- 備考 --}}
            <tr>
                <th>備考</th>
                <td>
                    <input type="text" name="note" value="{{ old('note', $attendance->note) }}">
                </td>
            </tr>
        </table>

        <div class="form-actions">
            <button type="submit" class="btn btn-approve">修正を保存</button>
        </div>
    </form>

    <div class="back-link mt-4">
        <a href="{{ route('admin.attendance.index') }}">← 日別勤怠一覧に戻る</a>
    </div>
</div>
@endsection
