@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/user/attendance-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    <table class="attendance-table">
        <tr>
            <th>名前</th>
            <td>{{ Auth::user()->name }}</td>
        </tr>
        <tr>
            <th>日付</th>
            @php
                $date = \Carbon\Carbon::parse($attendance->work_date);
            @endphp
            <td>{{ $date->format('Y年n月j日') }}</td>
        </tr>

        <tr>
            <th>出勤・退勤</th>
            <td>
                @if ($alreadyApplied)
                    {{ $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '--:--' }}
                    ～ 
                    {{ $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '--:--' }}
                @else
                    <input type="time" name="clock_in_time" value="{{ old('clock_in_time', $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '') }}">
                    ～ 
                    <input type="time" name="clock_out_time" value="{{ old('clock_out_time', $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '') }}">
                @endif
            </td>
        </tr>

        @foreach ($attendance->breakTimes as $i => $break)
            <tr>
                <th>休憩{{ $i + 1 }}</th>
                <td>
                    @if ($alreadyApplied)
                        {{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }} ～ {{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}
                    @else
                        <input type="time" name="break_start_times[{{ $i }}]" value="{{ old("break_start_times.$i", $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '') }}">
                        ～ 
                        <input type="time" name="break_end_times[{{ $i }}]"
                            value="{{ old("break_end_times.$i", $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
                    @endif
                </td>
            </tr>
        @endforeach

        <tr>
            <th>備考</th>
            <td>
                @if ($alreadyApplied)
                    {{ $attendance->note ?? '―' }}
                @else
                    <textarea name="note" rows="4" style="width: 50%;">{{ old('note', $attendance->note) }}</textarea>
                @endif
            </td>
        </tr>
    </table>

    {{-- フッター（申請済み or 修正用ボタン） --}}
    <div class="attendance-footer-right">
        @if ($alreadyApplied)
            <p class="apply-alert">※承認待ちのため修正はできません。</p>
        @else
            <form action="{{ route('attendance.apply', $attendance->id) }}" method="POST">
                @csrf
                <div class="apply-link">
                    <button type="submit" class="btn btn-apply">修正申請する</button>
                </div>
            </form>
        @endif

        <div class="back-link">
            <a href="{{ route('attendance.list') }}" class="btn btn-back">← 勤怠一覧</a>
        </div>
    </div>
</div>
@endsection
