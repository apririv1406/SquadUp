@extends('layouts.guest')

@section('title', 'Registro')

@section('content')
    <h5 class="fw-bold mb-4 text-center">Crea tu cuenta</h5>

    {{-- Opción de Registro Social (Google) --}}
    <div class="d-grid mb-4">
        {{-- Asume que la ruta socialite para Google es 'socialite.redirect', y lleva el parámetro 'google' --}}
        <a href="{{ route('socialite.redirect', 'google') }}" class="btn btn-outline-dark rounded-pill fw-bold py-2 d-flex justify-content-center align-items-center">
            <img src="https://img.icons8.com/color/24/000000/google-logo.png" alt="Google Logo" class="me-2" style="height: 20px;">
            Registrarse con Google
        </a>
    </div>

    <div class="d-flex align-items-center mb-4">
        <hr class="flex-grow-1">
        <span class="px-2 small text-muted">o usa tu email</span>
        <hr class="flex-grow-1">
    </div>

    {{-- Formulario de Registro Estándar --}}
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Campo Nombre --}}
        <div class="mb-3">
            <label for="name" class="form-label small fw-bold text-muted">Nombre Completo</label>
            <input type="text" name="name" id="name"
                   class="form-control rounded-pill @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Email --}}
        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-muted">Correo Electrónico</label>
            <input type="email" name="email" id="email"
                   class="form-control rounded-pill @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Contraseña --}}
        <div class="mb-3">
            <label for="password" class="form-label small fw-bold text-muted">Contraseña</label>
            <input type="password" name="password" id="password"
                   class="form-control rounded-pill @error('password') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo Confirmar Contraseña --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-bold text-muted">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control rounded-pill" required autocomplete="new-password">
        </div>

        {{-- Botón de Registro --}}
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-custom-green rounded-pill fw-bold py-2">
                Crear Cuenta
            </button>
        </div>
    </form>

    <hr class="my-3">

    <div class="text-center">
        <span class="small text-muted">¿Ya tienes cuenta?</span>
        <a href="{{ route('login') }}" class="small custom-text-green fw-bold">Inicia Sesión</a>
    </div>
@endsection
