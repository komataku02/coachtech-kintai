<form action="{{ route('register.store') }}" method="POST">
    @csrf

    <label>名前</label>
    <input type="text" name="name" value="{{ old('name') }}">
    @error('name') <div>{{ $message }}</div> @enderror

    <label>メールアドレス</label>
    <input type="email" name="email" value="{{ old('email') }}">
    @error('email') <div>{{ $message }}</div> @enderror

    <label>パスワード</label>
    <input type="password" name="password">
    @error('password') <div>{{ $message }}</div> @enderror

    <label>パスワード確認</label>
    <input type="password" name="password_confirmation">

    <button type="submit">登録</button>
</form>