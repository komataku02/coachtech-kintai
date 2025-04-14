@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">申請詳細</h2>

    <div class="mb-4">
        <p><strong>申請理由：</strong>{{ $application->request_reason }}</p>
        <p><strong>申請日：</strong>{{ $application->request_at }}</p>
        <p><strong>ステータス：</strong>{{ $application->status }}</p>
        @if($application->approved_at)
            <p><strong>承認日：</strong>{{ $application->approved_at }}</p>
        @endif
    </div>

    <div>
        <a href="{{ route('application.list') }}" class="text-blue-500 underline">← 申請一覧に戻る</a>
    </div>
</div>
@endsection
