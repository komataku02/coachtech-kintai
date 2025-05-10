@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="form-container">
    <h2 class="form-title">会員登録</h2>

    <form action="{{ route('register.store') }}" method="POST" class="form-box">
        @csrf

        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-input">
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-input">
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" class="form-input">
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">パスワード確認</label>
            <input type="password" name="password_confirmation" class="form-input">
        </div>

        <div class="form-group center">
            <button type="submit" class="submit-button">登録する</button>
        </div>

        <div class="form-group center">
            <a href="{{ route('login') }}" class="link-login">ログインはこちら</a>
        </div>
    </form>
</div>
@endsection
