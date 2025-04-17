<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
@yield('page-css')

</head>
<body>
    <header>
        <h1>勤怠管理アプリ</h1>
    </header>

    <main class="container">
        @if (session('message'))
            <div class="alert-success">
                {{ session('message') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} 勤怠管理アプリ
    </footer>
</body>
</html>
