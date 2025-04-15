@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="title">勤怠一覧</h2>

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
                <tr>
                    <td>{{ $attendance->work_date }}</td>
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
