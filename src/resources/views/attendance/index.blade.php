@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">勤怠登録</h2>

    <div class="mb-6">
        <p><strong>名前：</strong>{{ $user->name }}</p>
        <p><strong>今日の日付：</strong>{{ \Carbon\Carbon::today()->format('Y年m月d日') }}</p>
    </div>

    <div class="mb-6">
        <p><strong>現在のステータス：</strong>{{ $attendance->status ?? '勤務外' }}</p>
    </div>

    <div class="flex flex-wrap gap-4">
        <form method="POST" action="{{ route('attendance.clockIn') }}">
            @csrf
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">出勤</button>
        </form>

        <form method="POST" action="{{ route('attendance.breakIn') }}">
            @csrf
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">休憩入</button>
        </form>

        <form method="POST" action="{{ route('attendance.breakOut') }}">
            @csrf
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">休憩戻</button>
        </form>

        <form method="POST" action="{{ route('attendance.clockOut') }}">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">退勤</button>
        </form>
    </div>
</div>
@endsection
