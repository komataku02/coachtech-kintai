@extends('layouts.app')
@section('page-css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="title">勤怠一覧</h2>
    @php
    $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
    @endphp

    <table class="table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>ステータス</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                @php
                    $date = \Carbon\Carbon::parse($attendance->work_date);
                @endphp
            <tr>
                <td>{{ $date->format('Y年m月d日') }}（{{ $weekDays[$date->dayOfWeek] }}）</td>
                <td>{{ $attendance->clock_in_time ?? '-' }}</td>
                <td>{{ $attendance->clock_out_time ?? '-' }}</td>
                <td>{{ $attendance->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">勤怠情報がありません。</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
