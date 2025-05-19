@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin/admin-attendance-detail.css') }}">
@endsection

@section('content')
<div class="admin-attendance-detail">
    <h2 class="page-title">勤怠詳細</h2>

    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}" class="attendance-edit-form">
        @csrf
        @method('PUT')

        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>

            <tr>
                <th>日付</th>
                <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年n月j日') }}</td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="clock_in_time" class="form-input" value="{{ old('clock_in_time', \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i')) }}">
                    ～
                    <input type="time" name="clock_out_time" class="form-input" value="{{ old('clock_out_time', \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i')) }}">
                </td>
            </tr>

            @forelse ($attendance->breakTimes as $index => $break)
                <tr>
                    <th>休憩{{ $index + 1 }}</th>
                    <td>
                        <input type="time" name="break_start_times[]" class="form-input"
                            value="{{ old('break_start_times.' . $index, \Carbon\Carbon::parse($break->break_start)->format('H:i')) }}">
                        ～
                        <input type="time" name="break_end_times[]" class="form-input"
                            value="{{ old('break_end_times.' . $index, \Carbon\Carbon::parse($break->break_end)->format('H:i')) }}">
                    </td>
                </tr>
            @empty
                <tr>
                    <th>休憩1</th>
                    <td>
                        <input type="time" name="break_start_times[]" class="form-input">
                        ～
                        <input type="time" name="break_end_times[]" class="form-input">
                    </td>
                </tr>
            @endforelse

            <tr>
                <th>備考</th>
                <td>
                    <textarea name="note" class="form-textarea" rows="3">{{ old('note', $attendance->note) }}</textarea>
                </td>
            </tr>
        </table>

        <div class="form-actions">
            <button type="submit" class="btn btn-approve">修正</button>
        </div>
    </form>

    <div class="back-link">
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-back">← 勤怠一覧に戻る</a>
    </div>
</div>
@endsection

