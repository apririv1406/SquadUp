@extends('layouts.app')

@section('title', 'Gestión de Grupo: ' . $group->name)

@section('content')
<div class="container my-5 position-relative">

    {{-- BOTÓN SALIR DEL GRUPO (arriba a la derecha) --}}
    <button class="btn btn-outline-danger rounded-pill px-4 py-2 fw-bold shadow-sm"
            style="position:absolute; top:20px; right:20px;"
            onclick="openLeavePopup()">
        <i class="bi bi-box-arrow-left me-1"></i> Salir del grupo
    </button>

    {{-- POPUP DE CONFIRMACIÓN --}}
    <div id="leavePopup" class="popup-overlay d-none">
        <div class="popup-card rounded-4 shadow-lg p-4">

            <h4 class="fw-bold text-dark mb-3">¿Abandonar el grupo?</h4>

            <p class="text-muted mb-4">
                Si abandonas este grupo, también se eliminará tu participación en todos los eventos asociados.
            </p>

            <div class="d-flex justify-content-end gap-3">
                <button class="btn btn-success rounded-pill px-4" onclick="closeLeavePopup()">
                    Cancelar
                </button>

                <form action="{{ route('groups.leave', $group->group_id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        Sí, abandonar
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- HEADER --}}
    <header class="mb-4">
        <h1 class="display-5 fw-bold text-dark">{{ $group->name }}</h1>
        <p class="lead text-muted">Gestión del grupo, miembros y próximos eventos.</p>
    </header>

    {{-- MENSAJES --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="col-lg-8">

            {{-- TARJETA: ACERCA DEL GRUPO --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-success mb-3">Acerca del Grupo</h5>

                    <p class="text-muted mb-3">
                        {{ $group->description ?: 'No se ha proporcionado una descripción para este grupo.' }}
                    </p>

                    <p class="small text-muted mb-0">
                        <i class="bi bi-person-badge me-2 text-success"></i>
                        Organizador:
                        <span class="fw-semibold">{{ $group->organizer->name ?? 'Usuario Desconocido' }}</span>
                    </p>
                </div>
            </div>

            {{-- SECCIÓN DE EVENTOS --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-dark">Próximos Eventos</h4>

                @if ($isOrganizer)
                    <a href="{{ route('event.create', ['group_id' => $group->group_id]) }}"
                        class="btn fw-bold px-4 py-2 rounded-pill shadow-sm"
                        style="background-color: #2E7D32; color: white; border: 2px solid #4CAF50;">
                        <i class="bi bi-calendar-plus me-2"></i>
                        Nuevo Evento
                    </a>
                @endif
            </div>

            {{-- LISTADO DE EVENTOS --}}
            <div class="row g-4">
                @forelse ($group->events as $event)

                    @php
                        $eventDate = \Carbon\Carbon::parse($event->event_date);
                        $isPast = $eventDate->isPast();
                    @endphp

                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-0 rounded-4 {{ $isPast ? 'opacity-50' : 'hover-lift' }}">
                            <div class="card-body p-4">

                                @if ($isPast)
                                    <span class="badge bg-secondary text-white text-uppercase fw-bold mb-2">VENCIDO</span>
                                @endif

                                <h5 class="fw-bold text-dark text-truncate">{{ $event->title }}</h5>

                                <p class="small text-muted mb-1">
                                    <i class="bi bi-clock me-1 text-success"></i>
                                    {{ $eventDate->format('d M Y, H:i') }}
                                </p>

                                <p class="small text-muted mb-3">
                                    <i class="bi bi-geo-alt me-1 text-success"></i>
                                    {{ $event->location ?: 'Ubicación no definida' }}
                                </p>

                                <a href="#" class="btn btn-outline-success btn-sm rounded-pill w-100">
                                    Ver Detalles
                                </a>

                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-12">
                        <div class="alert alert-light text-center shadow-sm rounded-4 py-4">
                            <i class="bi bi-info-circle me-1"></i>
                            No hay eventos programados.
                            @if ($isOrganizer)
                                <strong>¡Crea el primero!</strong>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- COLUMNA LATERAL --}}
        <div class="col-lg-4">

            {{-- TARJETA: CÓDIGO DE INVITACIÓN --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4" style="background-color: #2E7D32;">
                <div class="card-body text-center p-4 text-white">

                    <i class="bi bi-share-fill h3 mb-2 d-block"></i>

                    <h5 class="fw-bold mb-3">Código de Invitación</h5>

                    <div class="bg-white text-dark fw-bold fs-3 py-3 px-4 rounded-3 shadow-sm border border-dark border-3 mb-3">
                        {{ $group->invitation_code }}
                    </div>

                    <p class="small text-white-75">
                        Comparte este código para que otros puedan unirse al grupo.
                    </p>

                </div>
            </div>

            {{-- TARJETA: MIEMBROS --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white fw-bold text-success">
                    Miembros del Grupo ({{ count($group->members) }})
                </div>

                <ul class="list-group list-group-flush">
                    @foreach ($group->members as $member)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-person-fill me-2 text-success"></i>
                                {{ $member->name }}
                            </span>

                            @if ($member->user_id === $group->organizer_id)
                                <span class="badge bg-success">Organizador</span>
                            @else
                                <span class="badge bg-secondary">Miembro</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
</div>

{{-- ESTILOS POPUP --}}
<style>
    .hover-lift:hover {
        transform: translateY(-5px);
        transition: 0.2s ease;
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, .15) !important;
    }

    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(4px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .popup-card {
        background: white;
        max-width: 420px;
        width: 90%;
    }
</style>

{{-- SCRIPT POPUP --}}
<script>
    function openLeavePopup() {
        document.getElementById('leavePopup').classList.remove('d-none');
    }

    function closeLeavePopup() {
        document.getElementById('leavePopup').classList.add('d-none');
    }
</script>

@endsection
