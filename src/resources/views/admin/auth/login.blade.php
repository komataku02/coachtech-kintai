@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="form-container">
    <h2 class="form-title">管理者ログイン</h2>

    <form action="{{ route('admin.login') }}" method="POST" class="form-box">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" class="form-input" value="{{ old('email') }}">
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

        <div class="form-group center">
            <button type="submit" class="submit-button">管理者ログインする</button>
        </div>
    </form>
</div>
@endsection
