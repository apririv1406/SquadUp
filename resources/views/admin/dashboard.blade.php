@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold mb-4">Panel de Administración</h1>

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        {{-- Gestión de Usuarios --}}
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Usuarios</div>
                <div class="card-body">
                    <a href="{{ route('users.index') }}" class="btn btn-primary w-100">
                        <i class="bi bi-people"></i> Gestionar Usuarios
                    </a>
                </div>
            </div>
        </div>

        {{-- Gestión de Eventos --}}
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Eventos</div>
                <div class="card-body">
                    <a href="{{ route('events.index') }}" class="btn btn-success w-100">
                        <i class="bi bi-calendar-event"></i> Gestionar Eventos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
