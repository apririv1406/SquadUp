@extends('layouts.app')

@section('title', 'Explorar Eventos')

@section('content')
    <div class="container my-5">

        {{-- Header --}}
        <header class="mb-4">
            <h1 class="display-5 fw-bold text-dark">Matchmaking: Explorar Eventos Públicos</h1>
            <p class="lead text-muted">Únete a partidos organizados por otros usuarios y conoce gente nueva.</p>
        </header>

        {{-- Mensajes de Sesión --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 1. Barra de Filtros Mejorada --}}
        <div class="card shadow-lg mb-5 p-4 border-0 rounded-4">
            <form method="GET" action="{{ route('events.explore') }}">
                <div class="row g-4">

                    {{-- Sección de Filtros Básicos: Deporte y Ubicación --}}
                    <div class="col-lg-6">
                        <h6 class="text-uppercase text-primary fw-bold mb-3">Filtros Esenciales</h6>
                        <div class="row g-3">
                            {{-- Filtro por Deporte --}}
                            <div class="col-sm-6">
                                <label for="sport" class="form-label text-muted small fw-bold">DEPORTE</label>
                                <select id="sport" name="sport" class="form-select rounded-3">
                                    <option value="">Todos los deportes</option>
                                    @foreach ($availableSports as $sport)
                                        <option value="{{ $sport }}" {{ $currentSport == $sport ? 'selected' : '' }}>
                                            {{ $sport }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro por Ubicación --}}
                            <div class="col-sm-6">
                                <label for="location" class="form-label text-muted small fw-bold">UBICACIÓN (Ciudad/Campo)</label>
                                <div class="input-group rounded-3 shadow-sm" style="overflow: hidden;">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-geo-alt-fill text-success"></i></span>
                                    <input type="text" id="location" name="location" value="{{ $currentLocation }}" placeholder="Introduce ciudad o campo..."
                                        class="form-control border-0" style="z-index: 1;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección de Rango de Capacidad --}}
                    <div class="col-lg-3">
                        <h6 class="text-uppercase text-primary fw-bold mb-3">Rango de Capacidad</h6>
                        <label class="form-label text-muted small fw-bold d-block">ASISTENTES (Mín. y Máx.)</label>
                        <div class="input-group">
                            <input type="number" id="min_capacity" name="min_capacity" value="{{ $minCapacity }}" placeholder="Mín."
                                class="form-control text-center" min="1" aria-label="Capacidad Mínima">
                            <span class="input-group-text bg-light border-start-0 border-end-0">
                                <i class="bi bi-arrow-left-right text-muted"></i>
                            </span>
                            <input type="number" id="max_capacity" name="max_capacity" value="{{ $maxCapacity }}" placeholder="Máx."
                                class="form-control text-center" min="1" aria-label="Capacidad Máxima">
                        </div>
                    </div>

                    {{-- Sección de Rango Horario (Calendarios) --}}
                    <div class="col-lg-3">
                        <h6 class="text-uppercase text-primary fw-bold mb-3">Rango Horario</h6>
                        <div class="row g-2">
                            {{-- Filtro por Fecha/Hora Mínima (Desde) --}}
                            <div class="col-12">
                                <label for="min_date" class="form-label text-muted small fw-bold">DESDE</label>
                                <input type="datetime-local" id="min_date" name="min_date" value="{{ $minDate }}"
                                    class="form-control rounded-3">
                            </div>

                            {{-- Filtro por Fecha/Hora Máxima (Hasta) --}}
                            <div class="col-12">
                                <label for="max_date" class="form-label text-muted small fw-bold">HASTA</label>
                                <input type="datetime-local" id="max_date" name="max_date" value="{{ $maxDate }}"
                                    class="form-control rounded-3">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fila de Botones de Acción --}}
                <div class="row mt-4 pt-3 border-top">
                    <div class="col-12 d-flex justify-content-end gap-3">
                        {{-- Limpiar solo si hay filtros activos --}}
                        @if ($currentSport || $currentLocation || $minCapacity || $maxCapacity || $minDate || $maxDate)
                            <a href="{{ route('events.explore') }}" class="btn btn-outline-danger rounded-pill px-4 shadow-sm">
                                <i class="bi bi-trash me-2"></i> Limpiar Filtros
                            </a>
                        @endif
                        <button type="submit" class="btn btn-success rounded-pill px-5 shadow-lg animate-hover-scale">
                            <i class="bi bi-search me-2"></i> Aplicar Búsqueda
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 2. Lista de Eventos --}}
        @if ($events->isEmpty())
            <div class="alert alert-secondary text-center py-5 border-dashed border-2">
                <i class="bi bi-info-circle display-4 mb-3 text-muted"></i>
                <p class="fs-5 text-muted">No se encontraron eventos públicos que coincidan con tus criterios.</p>
                <p class="text-sm">¡Sé el primero en crear uno!</p>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($events as $event)
                    {{-- Lógica para determinar si el evento ha pasado --}}
                    @php
                        // Convertir la fecha del evento a objeto Carbon
                        $eventDate = Carbon\Carbon::parse($event->event_date);
                        // Comparar con la fecha y hora actual
                        $isPast = $eventDate->isPast();
                        // Determinar si está lleno
                        $isFull = $event->capacity > 0 && $event->confirmed_attendees_count >= $event->capacity;

                        // Clase CSS condicional para atenuar eventos pasados
                        $cardClasses = $isPast ? 'opacity-50' : 'hover-lift';
                    @endphp

                    <div class="col">
                        {{-- Aplicar la clase de atenuación si el evento es pasado --}}
                        <div class="card h-100 shadow border-0 {{ $cardClasses }}"
                            style="border-radius: 1rem; transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.3s ease;">

                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    {{-- Badge de Deporte/Estado --}}
                                    @if ($isPast)
                                        <span class="badge bg-secondary text-white text-uppercase fw-bold">VENCIDO</span>
                                    @elseif ($isFull)
                                        <span class="badge bg-warning text-dark text-uppercase fw-bold">COMPLETO</span>
                                    @else
                                        {{-- El campo se llama sport_name --}}
                                        <span class="badge bg-primary text-white text-uppercase fw-bold">{{ $event->sport_name }}</span>
                                    @endif

                                    {{-- Aforo --}}
                                    <span class="text-muted small">
                                        <i class="bi bi-people-fill me-1 text-primary"></i>
                                        {{ $event->confirmed_attendees_count }}/{{ $event->capacity > 0 ? $event->capacity : '∞' }}
                                    </span>
                                </div>

                                <h5 class="card-title fw-bold text-dark mb-2">{{ $event->title }}</h5>
                                <p class="card-text text-muted small mb-3 text-truncate">{{ $event->description ?? 'Sin descripción.' }}</p>

                                <ul class="list-unstyled small mb-4">
                                    <li class="d-flex align-items-center mb-1 text-secondary">
                                        <i class="bi bi-calendar-event me-2 text-success"></i>
                                        {{ Carbon\Carbon::parse($event->event_date)->locale('es')->isoFormat('dddd, DD [de] MMM [a las] HH:mm') }}
                                    </li>
                                    <li class="d-flex align-items-center text-secondary">
                                        <i class="bi bi-geo-alt me-2 text-success"></i>
                                        {{ $event->location }}
                                    </li>
                                </ul>

                                {{-- Botones --}}
                                <div class="mt-auto d-flex gap-2">
                                    {{-- Enlace a Detalle (siempre disponible) --}}
                                    <a href="{{ route('event.show', $event->event_id) }}" class="btn btn-outline-secondary btn-sm flex-grow-1 rounded-pill">
                                        Ver Detalle
                                    </a>

                                    {{-- Botón Unirse (RF8, RF13) --}}
                                    @if ($isPast)
                                        <button disabled class="btn btn-secondary btn-sm rounded-pill flex-grow-1" title="Este evento ya ha ocurrido.">
                                            Pasado
                                        </button>
                                    @elseif ($isFull)
                                        <button disabled class="btn btn-warning btn-sm rounded-pill flex-grow-1">
                                            Aforo Completo
                                        </button>
                                    @else
                                        {{-- El formulario usa el método POST para unirse (RF8) --}}
                                        <form method="POST" action="{{ route('event.join', $event->event_id) }}" class="flex-grow-1">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm rounded-pill w-100 animate-hover-scale">
                                                ¡Unirse!
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-center mt-5">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    {{-- Script para animaciones y estilos --}}
    <style>
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
        }
        .animate-hover-scale:hover {
            transform: scale(1.03);
            transition: transform 0.2s ease-in-out;
        }
        /* Estilo para simular el rango de capacidad más visual */
        .input-group input {
            border-radius: 0.3rem !important;
        }
        .input-group input:first-child {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .input-group input:last-child {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        /* Clase para un borde sutil y punteado en la alerta de no resultados */
        .border-dashed {
            border-style: dashed !important;
        }
    </style>
@endsection
