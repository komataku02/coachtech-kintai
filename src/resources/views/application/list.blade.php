@extends('layouts.app')
@section('page-css')
<link rel="stylesheet" href="{{ asset('css/user/application-list.css') }}">
@endsection

@section('content')
<div class="application-wrapper">
    <div class="title-box">
        <h2 class="title">申請一覧</h2>
    </div>

    <div class="tab-box">
        <div class="tab-switch">
            <a href="{{ route('application.list', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'active' : '' }}">
                承認待ち
            </a>
            <a href="{{ route('application.list', ['status' => 'approved']) }}" class="{{ $status === 'approved' ? 'active' : '' }}">
                承認済み
            </a>
        </div>
        <div class="tab-underline"></div>
    </div>

    <div class="table-box">
        @if ($applications->isEmpty())
            <p class="notice">申請はまだありません。</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $app)
                        <tr>
                            <td>
                                @if ($app->status === 'pending')
                                    <span class="status pending">承認待ち</span>
                                @elseif ($app->status === 'approved')
                                    <span class="status approved">承認済</span>
                                @endif
                            </td>
                            <td>{{ $app->user->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($app->attendance->work_date)->format('Y/m/d') }}</td>
                            <td>{{ $app->note }}</td>
                            <td>{{ \Carbon\Carbon::parse($app->request_at)->format('Y/m/d') }}</td>
                            <td class="cell-center">
                                <a href="{{ route('attendance.show', $app->attendance->id) }}" class="link">詳細</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @if (!$applications->isEmpty())
        <div class="pagination">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
