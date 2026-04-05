@extends('layouts.app')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Open Sans', sans-serif;
    }

    body {
        min-height: 100vh;
        background-image: url('{{ asset("images/login.png") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    .login-container {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        padding: 30px 35px;
        width: 360px;
        background-color: rgba(255, 255, 255, 0.07);
        backdrop-filter: blur(5px);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .login-header {
        text-align: center;
        color: white;
        margin-bottom: 35px;
        padding-bottom: 15px;
        border-bottom: 3px solid #3498db;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 28px;
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .login-subtitle {
        text-align: center;
        color: white; /* Diubah menjadi putih */
        margin-bottom: 25px;
        font-size: 14px;
        font-weight: 400;
        opacity: 0.95;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        color: white; /* Diubah menjadi putih */
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 500;
    }

    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: none;
        background-color: rgba(255, 255, 255, 0.1);
        border-bottom: 2px solid #3498db;
        color: white;
        font-size: 16px;
        transition: all 0.3s ease;
        border-radius: 4px;
    }

    .form-input:focus {
        background-color: rgba(255, 255, 255, 0.15);
        border-bottom: 2px solid #64b5f6;
        outline: none;
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }

    .form-input::placeholder {
        color: rgba(255, 255, 255, 0.7); /* Diperjelas sedikit */
        font-size: 14px;
    }

    .login-button {
        width: 100%;
        padding: 14px;
        border: none;
        background-color: #3498db;
        font-size: 16px;
        color: white;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-top: 10px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }

    .login-button:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
    }

    .login-button:active {
        transform: translateY(0);
    }

    .login-links {
        text-align: center;
        color: white;
        font-size: 14px;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }

    .login-links a {
        color: white; /* Diubah menjadi putih */
        text-decoration: none;
        transition: color 0.3s ease;
        font-weight: 500;
        opacity: 0.9;
    }

    .login-links a:hover {
        color: #3498db;
        text-decoration: underline;
        opacity: 1;
    }

    .error-message {
        color: #ff6b6b;
        font-size: 12px;
        margin-top: 5px;
        background: rgba(255, 107, 107, 0.15);
        padding: 5px 10px;
        border-radius: 4px;
        border-left: 3px solid #ff6b6b;
    }

    .success-message {
        color: white;
        font-size: 14px;
        text-align: center;
        background: rgba(46, 204, 113, 0.8);
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 3px solid #2ecc71;
        backdrop-filter: blur(5px);
        font-weight: 500;
    }

    @media (max-width: 480px) {
        .login-container {
            width: 90%;
            padding: 25px 20px;
        }
        
        .login-header {
            font-size: 24px;
        }
        
        .login-subtitle {
            font-size: 13px;
        }
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="login-container">
    <h1 class="login-header">LOGIN</h1>
    
    <p class="login-subtitle">Sistem Peminjaman Laptop</p>
    
    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input 
                id="email" 
                type="email" 
                class="form-input @error('email') error @enderror" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus
                placeholder="email@example.com"
            >
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input 
                id="password" 
                type="password" 
                class="form-input @error('password') error @enderror" 
                name="password" 
                required 
                placeholder="••••••••"
            >
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="login-button">
            Login
        </button>
    </form>
    
    <div class="login-links">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Lupa Password?</a>
        @endif
        <div style="margin-top: 10px;">
            <span style="color: rgba(255, 255, 255, 0.9);">Belum punya akun? </span>
            <a href="{{ route('register') }}">Daftar di sini</a>
        </div>
    </div>
</div>

<script>
    // Add focus effect for inputs
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderBottom = '2px solid #64b5f6';
            this.style.boxShadow = '0 2px 5px rgba(100, 181, 246, 0.3)';
        });
        
        input.addEventListener('blur', function() {
            this.style.borderBottom = '2px solid #3498db';
            this.style.boxShadow = 'none';
        });
    });
    
    // Add loading state to button
    const loginForm = document.querySelector('form');
    const loginButton = document.querySelector('.login-button');
    
    if (loginForm && loginButton) {
        loginForm.addEventListener('submit', function() {
            loginButton.innerHTML = 'Loading...';
            loginButton.disabled = true;
            loginButton.style.opacity = '0.8';
            loginButton.style.background = '#2980b9';
        });
    }
</script>
@endsection