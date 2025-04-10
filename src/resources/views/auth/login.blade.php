@extends('layouts.app')

@section('content')
<div class="container">
    <h2>ログイン</h2>

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf

        <div>
            <label for="email">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email')
                <div>{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password">パスワード</label>
            <input type="password" name="password">
            @error('password')
                <div>{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">ログイン</button>
    </form>
</div>
@endsection
