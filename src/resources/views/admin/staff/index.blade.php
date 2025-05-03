@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 class="page-title">スタッフ一覧</h2>

    @if ($staff->isEmpty())
        <p class="no-data">スタッフ情報が見つかりません。</p>
    @else
        <table class="styled-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staff as $staff)
                    <tr>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>
                            <a href="{{ route('admin.staff.attendance', $staff->id) }}" class="btn-link">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
