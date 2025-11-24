<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <style>
        html {
            height: 100%;
        }

        .login-box {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 400px;
            padding: 40px;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, .5);
            box-sizing: border-box;
            box-shadow: 0 15px 25px rgba(0, 0, 0, .6);
            border-radius: 10px;
        }

        .login-box h2 {
            margin: 0 0 30px;
            padding: 0;
            color: #fff;
            text-align: center;
        }

        .login-box .user-box {
            position: relative;
        }

        .login-box .user-box input, select,
        button {
            width: 100%;
            padding: 10px 0;
            font-size: 16px;
            color: #fff;
            margin-bottom: 30px;
            border: none;
            border-bottom: 1px solid #fff;
            outline: none;
            background: transparent;
        }

        .login-box .user-box label {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px 0;
            font-size: 16px;
            color: #fff;
            pointer-events: none;
            transition: .5s;
        }

        .login-box .user-box input:focus~label,
        .login-box .user-box input:valid~label {
            top: -20px;
            left: 0;
            color: #03e9f4;
            font-size: 12px;
        }

        .login-box form a {
            position: relative;
            display: inline-block;
            padding: 10px 20px;
            color: #03e9f4;
            font-size: 16px;
            text-decoration: none;
            text-transform: uppercase;
            overflow: hidden;
            transition: .5s;
            margin-top: 40px;
            letter-spacing: 4px
        }

        .error {
            background: #ff4d4d;
            padding: 10px;
            border-radius: 5px;
            color: #fff;
            margin-bottom: 15px;
            text-align: center;
        }

        .alert-custom {
            padding: 12px 20px;
            font-weight: 500;
            text-align: center;
            margin-bottom: 15px;
            width: 100%;
            display: block;
        }

        /* Xato (error) uchun */
        .alert-custom.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        /* Muvaffaqiyat (success) uchun */
        .alert-custom.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</head>

<body>
    @if (session('error'))
        <div class="alert-custom error" role="alert">
            {{ session('error') }}
        </div>
    @elseif(session('success'))
        <div class="alert-custom success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="login-box">
        <h2>Talaba tizimiga kirish</h2>

        @if (session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('verifiy.sendMessage') }}" method="POST">
            @csrf
            <div class="user-box">
                <input type="number" name="talaba_id" value="{{ session('talaba_id') ?? old('talaba_id') }}" required>
                @error('talaba_id')
                    {{ $message }}
                @enderror
                <label>ID rqamingiz</label>
            </div>

            <div class="user-box">
                <input type="text" name="name" value="{{ session('name') ?? old('name') }}" required>
                @error('name')
                    {{ $message }}
                @enderror
                <label>Ismingiz</label>
            </div>

            <div class="user-box">
                <input type="text" name="guruh" value="{{ session('guruh') ?? old('guruh') }}" required>
                @error('guruh')
                    {{ $message }}
                @enderror
                <label>Guruhingiz</label>
            </div>

            <div class="user-box">
                <input type="text" name="take" value="{{ session('take') ?? old('take') }}" required>
                @error('take')
                    {{ $message }}
                @enderror
                <label>Telefon, Email, Telegram ....</label>
            </div>

             <div class="user-box">
                <select name="chat_id" id="">
                    <option value="" selected disabled>Parolni tiklaydigan shaxs</option>
                    @foreach ($admins as $admin)
                        @if ($admin->chat_id)
                            <option value="{{$admin->chat_id}}">{{$admin->name}}</option>
                        @endif
                    @endforeach
                </select>
                @error('chat_id')
                    {{ $message }}
                @enderror
            </div>

            <button style="cursor: pointer" type="submit">Yuborish</button>
        </form>
        <a href="{{route('verifiy.login')}}">Kirish</a>
    </div>
</body>

</html>
