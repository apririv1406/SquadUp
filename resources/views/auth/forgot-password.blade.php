@extends('layouts.guest')

@section('title', 'Recuperar Contraseña')

@section('content')
    <h5 class="fw-bold mb-4 text-center">¿Olvidaste tu Contraseña?</h5>

    <p class="small text-muted mb-4 text-center">
        Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
    </p>

    {{-- Session Status (Éxito del envío del email) --}}
    @if (session('status'))
        <div class="alert alert-success small" role="alert">
            {{ session('status') }}
        </div>
    @endif

    {{-- Formulario para solicitar el enlace --}}
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Campo Email --}}
        <div class="mb-4">
            <label for="email" class="form-label small fw-bold text-muted">Correo Electrónico</label>
            <input type="email" name="email" id="email"
                   class="form-control rounded-pill @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Botón de Enviar --}}
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-custom-green rounded-pill fw-bold py-2">
                Enviar Enlace de Restablecimiento
            </button>
        </div>
    </form>

    <hr class="my-3">

    <div class="text-center">
        <a href="{{ route('login') }}" class="small text-muted">Volver a Iniciar Sesión</a>
    </div>
@endsection
