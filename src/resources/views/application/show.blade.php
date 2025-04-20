@extends('layouts.app')
@section('page-css')
<link rel="stylesheet" href="{{ asset('css/application.css') }}">
@endsection


@section('content')
<div class="container">
    <h2 class="title">申請詳細</h2>

    <div class="detail-box">
        <p><span class="label">申請理由：</span>{{ $application->request_reason }}</p>
        <p><span class="label">申請日：</span>{{ \Carbon\Carbon::parse($application->request_at)->format('Y年m月d日') }}</p>
        <p><span class="label">ステータス：</span>
            @if ($application->status === 'pending')
                <span class="status pending">承認待ち</span>
            @elseif ($application->status === 'approved')
                <span class="status approved">承認済</span>
            @else
                <span class="status rejected">却下</span>
            @endif
        </p>
        @if($application->approved_at)
            <p><span class="label">承認日：</span>{{ \Carbon\Carbon::parse($application->approved_at)->format('Y年m月d日') }}</p>
        @endif
        
        @if ($application->status === 'pending')
            <p class="info-message">承認待ちのため修正はできません。</p>
        @endif
    </div>

    <div class="mt-3">
        <a href="{{ route('application.list') }}" class="link">&larr; 申請一覧に戻る</a>
    </div>
</div>
@endsection
