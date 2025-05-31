<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/common/reset.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/common/style.css') }}?v={{ time() }}">
    @yield('page-css')
</head>
<body>
    <header>
        <h1 class="app-title">COACHTECH</h1>
        @auth
        @php
            $user = Auth::user();
        @endphp

        <nav class="nav-header">
            <ul class="nav-list">
                @if ($user->role === 'admin')
                    <li><a href="{{ route('admin.attendance.list') }}">日別勤怠</a></li>
                    <li><a href="{{ route('admin.staff.list') }}">スタッフ一覧</a></li>
                    <li><a href="{{ route('admin.application.list') }}">申請一覧</a></li>
                @else
                    <li><a href="{{ route('attendance.index') }}">勤怠</a></li>
                    <li><a href="{{ route('attendance.list') }}">勤怠一覧</a></li>
                    <li><a href="{{ route('application.list') }}">申請一覧</a></li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-link nav-link-button">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
        @endauth
    </header>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <main class="main-content container">
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
