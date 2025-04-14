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
                    <th class="px-4 py-2 border">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $app)
                    <tr>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($app->request_at)->format('Y年m月d日') }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($app->attendance->work_date)->format('Y年m月d日') }}</td>
                        <td class="px-4 py-2 border">{{ $app->request_reason }}</td>
                        <td class="px-4 py-2 border">
                            @if ($app->status === 'pending')
                                <span class="text-yellow-600 font-semibold">承認待ち</span>
                            @elseif ($app->status === 'approved')
                                <span class="text-green-600 font-semibold">承認済</span>
                            @else
                                <span class="text-red-600 font-semibold">却下</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border text-center">
                            <a href="{{ route('application.detail', $app->id) }}" class="text-blue-500 hover:underline">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
