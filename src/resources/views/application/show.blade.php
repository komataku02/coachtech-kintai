@extends('layouts.app')
@section('page-css')
<link rel="stylesheet" href="{{ asset('css/application.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="title">申請詳細</h2>

    <table class="detail-table">
        <tr>
            <th>名前</th>
            <td>{{ $application->user->name }}</td>
        </tr>
        <tr>
            <th>日付</th>
            <td>{{ \Carbon\Carbon::parse($application->attendance->work_date)->format('Y年m月d日') }}</td>
        </tr>
        <tr>
            <th>出勤・退勤</th>
            <td>
                {{ $application->request_clock_in ? \Carbon\Carbon::createFromFormat('H:i', $application->request_clock_in, 'Asia/Tokyo')->format('H:i') : '-' }}
                ～
                {{ $application->request_clock_out ? \Carbon\Carbon::createFromFormat('H:i', $application->request_clock_out, 'Asia/Tokyo')->format('H:i') : '-' }}
            </td>
        </tr>

        @php
            $breaks = json_decode($application->request_breaks, true);
        @endphp
        @if (!empty($breaks))
            @foreach ($breaks as $index => $break)
                <tr>
                    <th>休憩{{ $index + 1 }}</th>
                    <td>{{ \Carbon\Carbon::createFromFormat('H:i', $break['start'])->format('H:i') }}
                        ～ {{ \Carbon\Carbon::createFromFormat('H:i', $break['end'])->format('H:i') }}
                    </td>
                </tr>
            @endforeach
        @endif

        <tr>
            <th>備考</th>
            <td>{{ $application->note }}</td>
        </tr>
    </table>

    @if ($application->status === 'pending')
        <p class="info-message">※ 承認待ちのため修正はできません。</p>
    @endif

    <div class="back-link">
        <a href="{{ route('application.list') }}" class="btn btn-back">← 申請一覧に戻る</a>
    </div>
</div>
@endsection
