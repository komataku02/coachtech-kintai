@extends('layouts.app')
@section('page-css')
<link rel="stylesheet" href="{{ asset('css/application.css') }}">
@endsection


@section('content')
<div class="container">
    <h2 class="title">申請一覧</h2>

    @if ($applications->isEmpty())
        <p class="notice">申請はまだありません。</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>申請日</th>
                    <th>対象日</th>
                    <th>理由</th>
                    <th>ステータス</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $app)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($app->request_at)->format('Y年m月d日') }}</td>
                        <td>{{ \Carbon\Carbon::parse($app->attendance->work_date)->format('Y年m月d日') }}</td>
                        <td>{{ $app->request_reason }}</td>
                        <td>
                            @if ($app->status === 'pending')
                                <span class="status pending">承認待ち</span>
                            @elseif ($app->status === 'approved')
                                <span class="status approved">承認済</span>
                            @else
                                <span class="status rejected">却下</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('application.detail', $app->id) }}" class="link">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
