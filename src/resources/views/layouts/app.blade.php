<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理アプリ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Laravel Mix や Vite 使用時 --}}
</head>
<body>
    <header>
        <h1>勤怠管理アプリ</h1>
    </header>

    <main class="container mx-auto p-4">
        @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif
        @yield('content')
    </main>

    <footer class="text-center text-sm mt-10 text-gray-500">
        &copy; {{ date('Y') }} 勤怠管理アプリ
    </footer>
</body>
</html>
