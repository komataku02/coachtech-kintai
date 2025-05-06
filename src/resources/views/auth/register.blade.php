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
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password">
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">パスワード確認</label>
            <input type="password" name="password_confirmation">
        </div>

        <div class="form-group text-center">
            <button type="submit" class="submit-button">登録</button>
        </div>
    </form>
</div>
@endsection
