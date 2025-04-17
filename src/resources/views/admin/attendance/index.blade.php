@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">日別勤怠一覧</h2>

    @if ($attendances->isEmpty())
        <p class="no-data">勤怠情報がありません。</p>
    @else
        <table class="styled-table">
            <thead>
                <tr>
                    <th>氏名</th>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>ステータス</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->user->name }}</td>
                        <td>{{ $attendance->work_date }}</td>
                        <td>{{ $attendance->clock_in_time ?? '-' }}</td>
                        <td>{{ $attendance->clock_out_time ?? '-' }}</td>
                        <td>{{ $attendance->status }}</td>
                        <td>
                            <a href="{{ route('admin.attendance.detail', $attendance->id) }}" class="btn-link">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <div class="pagination">
            {{ $attendances->links() }}
        </div>
</div>
@endsection
