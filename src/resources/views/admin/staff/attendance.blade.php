@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">{{ $user->name }}さんの月次勤怠</h2>

    {{-- CSVダウンロード --}}
    <div class="csv-download">
        <a href="{{ route('admin.staff.attendance.csv', $user->id) }}" class="btn-csv">CSVダウンロード</a>
    </div>

    {{-- 勤怠テーブル --}}
    <table class="styled-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>ステータス</th>
                <th>備考</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->work_date }}</td>
                    <td>{{ $attendance->clock_in_time ?? '-' }}</td>
                    <td>{{ $attendance->clock_out_time ?? '-' }}</td>
                    <td>{{ $attendance->status }}</td>
                    <td>{{ $attendance->note ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">勤怠情報がありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ステータスサマリー --}}
    <h3 class="sub-title">ステータス別 勤怠件数</h3>
    <table class="status-summary">
        <thead>
            <tr>
                <th>ステータス</th>
                <th>件数</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($statusCounts as $status => $count)
                <tr>
                    <td>{{ $status }}</td>
                    <td>{{ $count }} 件</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 戻るリンク --}}
    <div class="back-link">
        <a href="{{ route('admin.staff.list') }}">← スタッフ一覧に戻る</a>
    </div>
</div>
@endsection

