@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container admin-application-list">
    <h2 class="page-title">修正申請一覧</h2>

    <div class="tab-switch" id="application-tabs">
        <a href="{{ route('admin.application.list', ['status' => 'pending']) }}" class="tab-link {{ $status === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="{{ route('admin.application.list', ['status' => 'approved']) }}" class="tab-link {{ $status === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    @if ($applications->isEmpty())
        <p class="no-data">該当する申請はありません。</p>
    @else
        <table class="styled-table application-table">
            <thead>
                <tr>
                    <th>氏名</th>
                    <th>申請日</th>
                    <th>対象日</th>
                    <th>申請理由</th>
                    <th>状態</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $application)
                    <tr>
                        <td>{{ $application->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($application->request_at)->format('Y/m/d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($application->attendance->work_date)->format('Y/m/d') }}</td>
                        <td>{{ $application->note }}</td>
                        <td class="status-cell">
                            {{ $application->status === 'pending' ? '承認待ち' : '承認済み' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.application.detail', $application->id) }}" class="btn-link btn-detail">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $applications->links() }}
        </div>
    @endif

    <div class="back-link">
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-back">← 日別勤怠一覧に戻る</a>
    </div>
</div>
@endsection
