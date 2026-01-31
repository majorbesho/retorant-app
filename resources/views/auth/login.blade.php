@extends('layouts.app')

@section('content')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            margin: 0;
            padding: 0;
            background: fixed var(--primary-gradient);
            font-family: 'Outfit', 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        #app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        #app>main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            width: 100%;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            color: white;
        }

        .logo-area {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-icon {
            font-size: 3rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .login-subtitle {
            text-align: center;
            opacity: 0.8;
            margin-bottom: 2.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }

        .custom-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .custom-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            border-color: white;
        }

        .custom-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .invalid-feedback {
            color: #ff9b9b;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: block;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
            cursor: pointer;
        }

        .btn-submit {
            background: white;
            color: #764ba2;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 1.5rem;
            transition: all 0.3s;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            background: #f8f9fa;
        }

        .auth-links {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .auth-links a {
            color: white;
            font-weight: 700;
            text-decoration: underline;
        }

        .forgot-link {
            font-size: 0.85rem;
            opacity: 0.8;
            font-weight: 400 !important;
            text-decoration: none !important;
        }

        /* Login page navbar styling */
        .login-page .navbar {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-page .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .login-page .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .login-page .nav-link:hover {
            color: white !important;
        }

        /* Footer styling for login page */
        .login-page footer {
            background: rgba(0, 0, 0, 0.3) !important;
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('app').classList.add('login-page');
        });
    </script>

    <div class="login-wrapper">
        <div class="glass-card">
            <div class="logo-area">
                <i class="fas fa-lock logo-icon"></i>
                <h1 class="login-title">تسجيل الدخول</h1>
                <p class="login-subtitle">Welcome back. Please log in to your account.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group mb-4">
                    <label>البريد الإلكتروني (Email Address)</label>
                    <input type="email" name="email" class="custom-input @error('email') is-invalid @enderror"
                        placeholder="email@example.com" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label>كلمة المرور (Password)</label>
                        @if (Route::has('password.request'))
                            <a class="forgot-link text-white" href="{{ route('password.request') }}">
                                {{ __('نسيت كلمة المرور؟') }}
                            </a>
                        @endif
                    </div>
                    <input type="password" name="password" class="custom-input @error('password') is-invalid @enderror"
                        placeholder="Enter your password" required>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('تذكرني (Remember Me)') }}
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    دخول (Log In)
                </button>

                <div class="auth-links">
                    <span>ليس لديك حساب؟ (Don't have an account?)</span>
                    <a href="{{ route('register') }}">إنشاء حساب جديد (Sign Up)</a>
                </div>
            </form>
        </div>
    </div>
@endsection
