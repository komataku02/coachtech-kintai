@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">勤怠一覧</h2>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">日付</th>
                <th class="border px-4 py-2">出勤</th>
                <th class="border px-4 py-2">退勤</th>
                <th class="border px-4 py-2">ステータス</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                <tr>
                    <td class="border px-4 py-2">{{ $attendance->work_date }}</td>
                    <td class="border px-4 py-2">{{ $attendance->clock_in_time ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $attendance->clock_out_time ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $attendance->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4">勤怠情報がありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
