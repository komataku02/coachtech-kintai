@extends('layouts.app')

@section('content')
<div class="container">
    <h2>申請詳細（管理者）</h2>

    <p><strong>申請者：</strong>{{ $application->user->name }}</p>
    <p><strong>日付：</strong>{{ $application->attendance->work_date }}</p>
    <p><strong>申請理由：</strong>{{ $application->request_reason }}</p>
    <p><strong>ステータス：</strong>{{ $application->status }}</p>

    @if ($application->status === 'pending')
        <form method="POST" action="{{ route('admin.application.approve',['id' => $application->id]) }}">
            @csrf
            <button type="submit">承認する</button>
        </form>
    @else
        <p>この申請はすでに処理済みです。</p>
    @endif

    <div>
        <a href="{{ route('admin.application.list') }}">← 一覧に戻る</a>
    </div>
</div>
@endsection
