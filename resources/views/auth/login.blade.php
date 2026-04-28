<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .login-title {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
        }

        .login-subtitle {
            margin: 0 0 24px;
            color: #6b7280;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            height: 44px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 0 14px;
            outline: none;
        }

        input:focus {
            border-color: #2563eb;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            color: #4b5563;
            font-size: 14px;
        }

        .btn-login {
            width: 100%;
            height: 46px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #1d4ed8;
        }

        .alert {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .field-error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h1 class="login-title">Login</h1>
        <p class="login-subtitle">Sign in with your username and password.</p>

        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label for="username">Username</label>
                <input
                    id="username"
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                >
                @error('username')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                >
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-row">
                <input id="remember" type="checkbox" name="remember" value="1">
                <label for="remember" style="margin: 0; font-weight: 500;">Remember me</label>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>
</body>
</html>