@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/user/attendance-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    @if ($alreadyApplied)
        <table class="attendance-table">
            <tr>
                <th>名前</th>
                <td>{{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年n月j日') }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{ $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '--:--' }}
                    ～
                    {{ $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '--:--' }}
                </td>
            </tr>
            @foreach ($attendance->breakTimes as $i => $break)
                <tr>
                    <th>休憩{{ $i + 1 }}</th>
                    <td>{{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }} ～ {{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}</td>
                </tr>
            @endforeach
            <tr>
                <th>備考</th>
                <td>{{ $attendance->note ?? '―' }}</td>
            </tr>
        </table>

        <p class="apply-alert">※承認待ちのため修正はできません。</p>

        <div class="back-link">
            <a href="{{ route('attendance.list') }}" class="btn btn-back">← 勤怠一覧</a>
        </div>

    @else
        <form action="{{ route('application.store') }}" method="POST">
            @csrf
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            <table class="attendance-table">
                <tr>
                    <th>名前</th>
                    <td>{{ Auth::user()->name }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年n月j日') }}</td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="clock_in_time" value="{{ old('clock_in_time', $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '') }}">
                        @error('clock_in_time')
                            <div class="error-message">{{ $message }}</div>
                        @enderror

                        ～

                        <input type="time" name="clock_out_time" value="{{ old('clock_out_time', $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '') }}">
                        @error('clock_out_time')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>

                @foreach ($attendance->breakTimes as $i => $break)
                    <tr>
                        <th>休憩{{ $i + 1 }}</th>
                        <td>
                            <input type="time" name="break_start_times[{{ $i }}]" value="{{ old("break_start_times.$i", $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '') }}">
                            @error("break_start_times.$i")
                                <div class="error-message">{{ $message }}</div>
                            @enderror

                            ～

                            <input type="time" name="break_end_times[{{ $i }}]" value="{{ old("break_end_times.$i", $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
                            @error("break_end_times.$i")
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th>休憩{{ count($attendance->breakTimes) + 1 }}</th>
                    <td>
                        <input type="time" name="break_start_times[{{ count($attendance->breakTimes) }}]" value="{{ old('break_start_times.' . count($attendance->breakTimes)) }}">
                        @error('break_start_times.' . count($attendance->breakTimes))
                            <div class="error-message">{{ $message }}</div>
                        @enderror

                    ～

                        <input type="time" name="break_end_times[{{ count($attendance->breakTimes) }}]" value="{{ old('break_end_times.' . count($attendance->breakTimes)) }}">
                        @error('break_end_times.' . count($attendance->breakTimes))
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="note" rows="4" style="width: 50%;">{{ old('note', $attendance->note) }}</textarea>
                        @error('note')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
            </table>

            <div class="attendance-footer-right">
                <button type="submit" class="btn btn-apply">修正</button>
                <div class="back-link">
                    <a href="{{ route('attendance.list') }}" class="btn btn-back">← 勤怠一覧</a>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
