@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">申請一覧</h2>

    @if ($applications->isEmpty())
        <p>申請はまだありません。</p>
    @else
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">申請日</th>
                    <th class="px-4 py-2 border">対象日</th>
                    <th class="px-4 py-2 border">理由</th>
                    <th class="px-4 py-2 border">ステータス</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $app)
                    <tr>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($app->request_at)->format('Y年m月d日') }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($app->attendance->work_date)->format('Y年m月d日') }}</td>
                        <td class="px-4 py-2 border">{{ $app->request_reason }}</td>
                        <td class="px-4 py-2 border">{{ $app->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
