@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="container my-5">

    <h1 class="fw-bold mb-5 text-center">Panel de Administración</h1>

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <div class="row justify-content-center g-4">

        {{-- Tarjeta Usuarios --}}
        <div class="col-md-5">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="admin-card shadow-sm border-0 rounded-4 p-4 text-center">

                    <div class="admin-icon mb-3">
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <h3 class="fw-bold mb-2">Usuarios</h3>
                    <p class="text-muted">Gestiona los usuarios registrados en la plataforma</p>

                </div>
            </a>
        </div>

        {{-- Tarjeta Eventos --}}
        <div class="col-md-5">
            <a href="{{ route('admin.events.index') }}" class="text-decoration-none">
                <div class="admin-card shadow-sm border-0 rounded-4 p-4 text-center">

                    <div class="admin-icon mb-3">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>

                    <h3 class="fw-bold mb-2">Eventos</h3>
                    <p class="text-muted">Administra todos los eventos creados por los usuarios</p>

                </div>
            </a>
        </div>

    </div>
</div>

{{-- ESTILOS PERSONALIZADOS --}}
<style>
    .admin-card {
        background: white;
        transition: all 0.25s ease;
        border: 2px solid transparent;
    }

    .admin-card:hover {
        transform: translateY(-6px);
        border-color: #4CAF50;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .admin-icon {
        font-size: 4rem;
        color: #2E7D32;
        transition: color 0.25s ease;
    }

    .admin-card:hover .admin-icon {
        color: #4CAF50;
    }
</style>

@endsection
