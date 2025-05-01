@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="container text-center">
    <div class="message-box">
        <p>登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>

        {{-- MailHog を新しいタブで開く --}}
        <div class="btn-wrapper">
            <a href="http://localhost:8025/" target="_blank" class="btn-verify">認証はこちらから</a>
        </div>

        {{-- 認証メール再送ボタン --}}
        <div class="resend-link">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="link-resend">認証メールを再送する</button>
            </form>
        </div>
    </div>
</div>
@endsection
