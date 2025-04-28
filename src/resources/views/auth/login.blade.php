@extends('layouts.app')
@section('page-css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection


@section('content')
<div class="form-container">
    <h2 class="form-title">ログイン</h2>

    <form action="{{ route('login.submit') }}" method="POST" class="form-box">
        @csrf

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

        <div class="form-group text-center">
            <button type="submit" class="submit-button">ログイン</button>
        </div>

        <div class="form-group">
            <a 
        </div>
    </form>
</div>
@endsection
