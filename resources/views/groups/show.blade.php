@extends('layouts.app')

@section('title', 'Gestión de Grupo: ' . $group->name)

@section('content')
<div class="container-fluid py-5">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">

        {{-- COLUMNA PRINCIPAL (EVENTOS) --}}
        <div class="col-lg-8">
            <h1 class="display-5 fw-bold text-dark mb-4">{{ $group->name }}</h1>

            {{-- Descripción --}}
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted fw-bold">Acerca del Grupo</h5>
                    <p class="card-text">{{ $group->description ?? 'No se ha proporcionado una descripción para este grupo.' }}</p>
                    <hr>
                    <p class="small text-muted mb-0">Organizador: {{ $group->organizer->name ?? 'Usuario Desconocido' }}</p>
                </div>
            </div>

            {{-- Sección de Eventos --}}
            <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
                <h2 class="h4 fw-bold">Próximos Eventos ({{ count($group->events) }})</h2>
                @if ($isOrganizer)
                    {{-- Botón de Crear Evento (RF1) --}}
                    <a href="{{ route('events.create', ['group_id' => $group->group_id]) }}" class="btn btn-custom-green rounded-pill fw-bold">
                        <i class="bi bi-calendar-plus me-2"></i> Crear Nuevo Evento
                    </a>
                @endif
            </div>

            {{-- Listado de Eventos --}}
            <div class="row">
                @forelse ($group->events as $event)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-start border-custom-green border-4">
                            <div class="card-body">
                                <h5 class="card-title fw-bold text-truncate">{{ $event->title }}</h5>
                                <p class="card-text small text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y, H:i') }}
                                </p>
                                <p class="card-text small text-muted">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $event->location ?? 'Ubicación no definida' }}
                                </p>
                                <a href="#" class="btn btn-sm btn-outline-custom-green mt-2">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light text-center" role="alert">
                            <i class="bi bi-info-circle me-1"></i> Aún no hay eventos programados.
                            @if ($isOrganizer)
                                ¡Sé el primero en crear uno!
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- COLUMNA LATERAL (MIEMBROS y CÓDIGO) --}}
        <div class="col-lg-4">

            {{-- Tarjeta de Código de Invitación (RF8) --}}
            <div class="card shadow-lg bg-custom-green-light border-0 mb-4" style="border-radius: 1rem;">
                <div class="card-body text-white text-center p-4">
                    <i class="bi bi-share-fill h3 mb-2 d-block"></i>
                    <h5 class="fw-bold mb-1">Código de Invitación</h5>
                    <div class="p-2 bg-white text-dark rounded-3 fw-bolder fs-4 shadow-sm my-3 border border-dark border-3">
                        {{ $group->invitation_code }}
                    </div>
                    <p class="small mb-0">Comparte este código con tus amigos para que se unan.</p>
                </div>
            </div>

            {{-- Tarjeta de Miembros --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">
                    Miembros del Grupo ({{ count($group->members) }})
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($group->members as $member)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-person-fill me-2"></i>
                                {{ $member->name }}
                            </span>
                            @if ($member->user_id === $group->organizer_id)
                                <span class="badge bg-custom-green">Organizador</span>
                            @else
                                <span class="badge bg-secondary-light text-dark">Miembro</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection
