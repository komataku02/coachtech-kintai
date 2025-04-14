@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-4">修正申請フォーム</h2>

    <div class="mb-4">
        <p><strong>日付：</strong>{{ $attendance->work_date }}</p>
        <p><strong>出勤：</strong>{{ $attendance->clock_in_time ?? '未記録' }}</p>
        <p><strong>退勤：</strong>{{ $attendance->clock_out_time ?? '未記録' }}</p>
        <p><strong>ステータス：</strong>{{ $attendance->status }}</p>
    </div>

    @if ($alreadyApplied)
    <p class="text-red-500 font-semibold">この勤怠には既に申請を送信済みです。</p>
@else
    <form method="POST" action="{{ route('application.store') }}">
        @csrf
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

        <div class="mb-4">
            <label for="request_reason" class="block mb-1 font-semibold">申請理由</label>
            <textarea name="request_reason" id="request_reason" rows="4" required class="w-full border border-gray-300 p-2 rounded">{{ old('request_reason') }}</textarea>
            @error('request_reason')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">申請する</button>
    </form>
@endif
</div>
@endsection
