@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container admin-staff-list">
    <h2 class="page-title">スタッフ一覧</h2>

    @if ($staff->isEmpty())
        <p class="no-data">スタッフ情報が見つかりません。</p>
    @else
        <table class="styled-table staff-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staff as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>
                            <a href="{{ route('admin.staff.attendance', $member->id) }}" class="btn-link btn-detail">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
