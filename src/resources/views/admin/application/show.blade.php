@extends('layouts.app')
@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection


@section('content')
<div class="container">
    <h2 class="title">勤怠詳細</h2>

    <table class="detail-table">
        <tr>
            <th>名前</th>
            <td>{{ $application->user->name }}</td>
        </tr>
        <tr>
            <th>日付</th>
            <td>{{ \Carbon\Carbon::parse($application->attendance->work_date)->format('Y年n月j日') }}</td>
        </tr>
        <tr>
            <th>出勤・退勤</th>
            <td>
                {{ $clockIn ?? '--:--' }} ～ {{ $clockOut ?? '--:--' }}
            </td>
        </tr>

        {{-- 休憩時間 --}}
        @forelse ($application->attendance->breakTimes as $index => $break)
        <tr>
            <th>休憩{{ $index + 1 }}</th>
            <td>
                {{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }}
                ～ 
                {{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}
            </td>
        </tr>
        @empty
        <tr>
            <th>休憩</th>
            <td></td>
        </tr>
        @endforelse

        {{-- 修正理由（note → request_reason） --}}
        <tr>
            <th>修正理由</th>
            <td>{{ $application->request_reason ?? '記載なし' }}</td>
        </tr>
    </table>

    {{-- 承認済 or 承認ボタン --}}
    <div class="form-actions">
        @if ($application->status === 'pending')
            <form method="POST" action="{{ route('admin.application.approve', ['id' => $application->id]) }}">
                @csrf
                <button type="submit" class="btn btn-approve">承認する</button>
            </form>
        @else
            <p class="status-label">承認済み</p>
        @endif
    </div>

    <div class="back-link">
        <a href="{{ route('admin.application.list') }}">← 申請一覧に戻る</a>
    </div>
</div>
@endsection
