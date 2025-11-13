@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container my-5">
        <header class="mb-5">
            <h1 class="display-4 fw-bold text-dark">¡Hola, {{ Auth::user()->name }}!</h1>
            <p class="lead text-muted">Bienvenido de nuevo. Aquí tienes un resumen de tu actividad.</p>
        </header>

        {{-- Mensajes de Sesión --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 1. Tarjetas de Balance Global (RNF4) --}}
        <h2 class="h4 fw-bold text-dark mb-4">Balance Global (SquadUp)</h2>
        <div class="row g-4 mb-5">

            {{-- Tarjeta de Crédito (Te deben) --}}
            <div class="col-md-4">
                <div class="card h-100 p-3 shadow-sm border-success border-3 border-start" style="border-radius: 1rem;">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Te Deben (Crédito)</p>
                        <h3 class="card-title fw-bolder text-success display-6">+{{ number_format($globalBalance['credit'], 2) }}€</h3>
                        <p class="card-text small text-muted">Suma de lo que el resto de usuarios te debe.</p>
                    </div>
                </div>
            </div>

            {{-- Tarjeta de Deuda (Debes) --}}
            <div class="col-md-4">
                <div class="card h-100 p-3 shadow-sm border-danger border-3 border-start" style="border-radius: 1rem;">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Debes (Deuda)</p>
                        <h3 class="card-title fw-bolder text-danger display-6">-{{ number_format($globalBalance['debt'], 2) }}€</h3>
                        <p class="card-text small text-muted">Suma de lo que debes a otros usuarios.</p>
                    </div>
                </div>
            </div>

            {{-- Tarjeta de Saldo Neto --}}
            <div class="col-md-4">
                <div class="card h-100 p-3 shadow-lg border-primary border-3 border-start" style="border-radius: 1rem;">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Saldo Neto</p>
                        <h3 class="card-title fw-bolder {{ $globalBalance['net'] >= 0 ? 'text-success' : 'text-danger' }} display-6">
                            {{ $globalBalance['net'] >= 0 ? '+' : '' }}{{ number_format($globalBalance['net'], 2) }}€
                        </h3>
                        <p class="card-text small text-muted">Tu posición total. ¡Hora de liquidar!</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Próximos Eventos y Acciones --}}
        <h2 class="h4 fw-bold text-dark mb-4">Tus Próximos Eventos</h2>
        <div class="row g-4">

            {{-- Columna de Eventos --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0" style="border-radius: 1rem;">
                    <div class="card-header bg-white border-bottom fw-bold p-3" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                        Eventos Confirmados
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse ($upcomingEvents as $event)
                            <li class="list-group-item d-flex justify-content-between align-items-center hover-bg-light">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">{{ $event->title }}</h6>
                                    <span class="badge bg-secondary me-2">{{ $event->sport }}</span>
                                    <span class="text-muted small"><i class="bi bi-calendar me-1"></i> {{ Carbon\Carbon::parse($event->event_date)->locale('es')->isoFormat('DD/MM [a las] HH:mm') }}</span>
                                </div>
                                <a href="{{ route('event.show', $event->event_id) }}" class="btn btn-outline-success btn-sm rounded-pill">Ver Detalle</a>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-4 text-muted">
                                No tienes eventos confirmados próximos.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Columna de Acciones Rápidas --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0" style="border-radius: 1rem;">
                    <div class="card-header bg-white border-bottom fw-bold p-3" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                        Acciones Rápidas
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('events.explore') }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                            <i class="bi bi-search me-3 fs-5 text-primary"></i>
                            Explorar Eventos (Matchmaking)
                        </a>
                        <a href="{{ route('event.create') }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                            <i class="bi bi-calendar-plus me-3 fs-5 text-success"></i>
                            Crear Nuevo Evento
                        </a>
                        <a href="{{ route('groups.create') }}" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                            <i class="bi bi-people me-3 fs-5 text-info"></i>
                            Crear Nuevo Grupo
                        </a>
                        <a href="#" class="list-group-item list-group-item-action py-3 d-flex align-items-center">
                            <i class="bi bi-cash me-3 fs-5 text-warning"></i>
                            Ver Historial de Liquidaciones
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa; /* Bootstrap light gray */
            transition: background-color 0.2s ease-in-out;
        }
    </style>
@endsection
