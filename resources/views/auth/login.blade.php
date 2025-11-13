@extends('layouts.guest')

@section('title', 'Inicia Sesión')

@section('content')
    <h5 class="fw-bold mb-4 text-center">Inicia Sesión en SquadUp</h5>

    {{-- Session Status (Mensajes de error/éxito de la sesión) --}}
    @if (session('status'))
        <div class="alert alert-success small" role="alert">
            {{ session('status') }}
        </div>
    @endif

    {{-- Manejo de errores de Socialite (si la autenticación de Google falla) --}}
    @if ($errors->has('error'))
        <div class="alert alert-danger small" role="alert">
            {{ $errors->first('error') }}
        </div>
    @endif

    {{-- Formulario de Login --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Campo Email --}}
        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-muted">Correo Electrónico</label>
            <input type="email" name="email" id="email"
                   class="form-control rounded-pill @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Contraseña --}}
        <div class="mb-3">
            <label for="password" class="form-label small fw-bold text-muted">Contraseña</label>
            <input type="password" name="password" id="password"
                   class="form-control rounded-pill @error('password') is-invalid @enderror"
                   required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Recordar Contraseña --}}
        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
            <label class="form-check-label small text-muted" for="remember_me">
                Recordarme
            </label>
        </div>

        {{-- Botón de Acceso (Con la clase de color personalizada) --}}
        <div class="d-grid gap-2 mb-4">
            <button type="submit" class="btn btn-custom-green rounded-pill fw-bold py-2">
                Acceder
            </button>
        </div>
    </form>

    <!-- DIVISOR Y BOTÓN DE GOOGLE -->
    <div class="text-center my-4">
        <span class="small text-muted">o inicia sesión con</span>
    </div>

    {{-- Botón de Google --}}
    <div class="d-grid gap-2">
        <a href="{{ route('google.login') }}" class="btn btn-outline-secondary rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center">
            {{-- Ícono de Google (Usando SVG de Google para mejor calidad/integración) --}}
            <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.62-.05-1.22-.16-1.81H12v3.66h5.8c-.24 1.18-.84 2.19-1.79 2.89v2.37h3.05c1.79-1.66 2.82-4.07 2.82-7.01z" fill="#4285F4"/>
                <path d="M12 23c3.24 0 5.95-1.07 7.93-2.91l-3.05-2.37c-.85.57-1.95.89-3.32.89-2.58 0-4.78-1.74-5.58-4.08H3.35v2.46c1.92 3.82 5.92 6.51 10.65 6.51z" fill="#34A853"/>
                <path d="M6.42 14.4c-.18-.57-.28-1.18-.28-1.8s.1-1.23.28-1.8V8.35H3.35c-.58 1.15-.9 2.45-.9 3.85s.32 2.7.9 3.85l3.07-2.4z" fill="#FBBC05"/>
                <path d="M12 5.56c1.47 0 2.78.53 3.82 1.49l2.71-2.61C16.95 2.5 14.24 1 12 1 7.27 1 3.27 3.69 1.35 7.5l3.07 2.4c.8-2.34 3-4.08 5.58-4.08z" fill="#EA4335"/>
            </svg>
            Continuar con Google
        </a>
    </div>

    {{-- Enlaces Adicionales --}}
    <div class="text-center mt-3">
        @if (Route::has('password.request'))
            <a class="text-muted small" href="{{ route('password.request') }}">
                ¿Olvidaste tu contraseña?
            </a>
        @endif
    </div>
    <hr class="my-3">
    <div class="text-center">
        <span class="small text-muted">¿No tienes cuenta?</span>
        <a href="{{ route('register') }}" class="small custom-text-green fw-bold">Regístrate</a>
    </div>
@endsection
