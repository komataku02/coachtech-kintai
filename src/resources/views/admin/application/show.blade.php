@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin/admin-application-approve.css') }}">
@endsection

@section('content')
<div class="container admin-application-detail">
    <h2 class="page-title">勤怠詳細</h2>

    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table class="application-detail-table">
        <tr><th>名前</th><td>{{ $application->user->name }}</td></tr>
        <tr><th>日付</th><td>{{ \Carbon\Carbon::parse($application->attendance->work_date)->format('Y年n月j日') }}</td></tr>
        <tr><th>出勤・退勤</th><td>{{ $clockIn ?? '--:--' }} ～ {{ $clockOut ?? '--:--' }}</td></tr>

        @php
            $breaks = $application->attendance->breakTimes->sortBy('break_start')->values();
        @endphp
        @forelse ($breaks as $index => $break)
            <tr><th>休憩{{ $index + 1 }}</th>
                <td>{{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }} ～ {{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}</td>
            </tr>
        @empty
            <tr><th>休憩</th><td>記録なし</td></tr>
        @endforelse

        <tr><th>備考</th><td>{{ $application->note }}</td></tr>
        @if ($application->approved_at)
            <tr><th>承認日時</th><td>{{ \Carbon\Carbon::parse($application->approved_at)->format('Y年n月j日 H:i') }}</td></tr>
        @endif
    </table>

    <div class="approval-section">
        @if ($application->status === 'pending')
            <form method="POST" action="{{ route('admin.application.approve', $application->id) }}">
                @csrf
                <button type="submit" class="btn btn-approve">承認</button>
            </form>
        @else
            <p class="status-label approved">承認済み</p>
        @endif
    </div>

    <div class="back-link-container">
        <a href="{{ route('admin.application.list') }}" class="btn btn-back">← 申請一覧</a>
    </div>
</div>
@endsection