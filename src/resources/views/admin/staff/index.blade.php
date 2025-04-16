@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="page-title">スタッフ一覧</h2>

    @if ($staffs->isEmpty())
        <p class="no-data">スタッフ情報が見つかりません。</p>
    @else
        <table class="styled-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>アクション</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staffs as $staff)
                    <tr>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>
                            <a href="{{ route('admin.staff.attendance', $staff->id) }}" class="btn-link">月次勤怠一覧</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
