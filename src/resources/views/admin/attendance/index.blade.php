@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="page-title">日別勤怠一覧</h2>

    @if ($attendances->isEmpty())
        <p>勤怠情報が見つかりませんでした。</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>日付</th>
                    <th>ユーザー名</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>ステータス</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->work_date }}</td>
                        <td>{{ $attendance->user->name ?? '未登録' }}</td>
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

        <div class="pagination">
            {{ $attendances->links() }}
        </div>
    @endif
</div>
@endsection
