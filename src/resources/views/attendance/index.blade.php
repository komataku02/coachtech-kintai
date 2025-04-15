@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="title">勤怠登録</h2>

    <div class="user-info">
        <p><strong>名前：</strong>{{ $user->name }}</p>
        <p><strong>今日の日付：</strong>{{ \Carbon\Carbon::today()->format('Y年m月d日') }}</p>
    </div>

    <div class="status-info">
        <p><strong>現在のステータス：</strong>{{ $attendance->status ?? '勤務外' }}</p>
    </div>

    <div class="button-group">
        <form method="POST" action="{{ route('attendance.clockIn') }}">
            @csrf
            <button type="submit" class="btn blue">出勤</button>
        </form>

        <form method="POST" action="{{ route('attendance.breakIn') }}">
            @csrf
            <button type="submit" class="btn yellow">休憩入</button>
        </form>

        <form method="POST" action="{{ route('attendance.breakOut') }}">
            @csrf
            <button type="submit" class="btn green">休憩戻</button>
        </form>

        <form method="POST" action="{{ route('attendance.clockOut') }}">
            @csrf
            <button type="submit" class="btn red">退勤</button>
        </form>
    </div>
</div>
@endsection
