@extends('layouts.app')

@section('title', 'Mis Grupos')

@section('content')
<div class="container my-5">

    {{-- HEADER --}}
    <header class="mb-4">
        <h1 class="display-5 fw-bold text-dark">Mis Grupos</h1>
        <p class="lead text-muted">Accede a tus grupos, consulta sus eventos y gestiona tus comunidades.</p>
    </header>

    {{-- UNIRSE A UN GRUPO POR CÓDIGO --}}
    <div class="card shadow-sm border-0 rounded-4 mb-5 hover-lift">
        <div class="card-body p-4">

            <h4 class="fw-bold text-dark mb-3">
                <i class="bi bi-key-fill text-success me-2"></i>
                Unirse a un grupo con código
            </h4>

            {{-- Mensajes --}}
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3">
                <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show rounded-3">
                <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('groups.joinByCode') }}" method="POST" class="mt-3">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Código de invitación</label>
                    <input type="text"
                        name="invitation_code"
                        class="form-control rounded-3"
                        placeholder="Introduce el código..."
                        required>
                </div>

                <button type="submit"
                    class="btn fw-bold px-4 py-2 rounded-pill"
                    style="background-color:#2E7D32; color:white; border:2px solid #4CAF50;">
                    Unirse al grupo
                </button>
            </form>

        </div>
    </div>


    {{-- MENSAJES --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- LISTA DE GRUPOS --}}
    @if ($groups->isEmpty())
    <div class="alert alert-light text-center shadow-sm rounded-4 py-5">
        <i class="bi bi-info-circle display-6 text-muted mb-3 d-block"></i>
        <p class="fs-5 text-muted">Todavía no perteneces a ningún grupo.</p>
        <p class="text-muted small">Puedes crear uno o unirte mediante un código de invitación.</p>
    </div>
    @else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        @foreach ($groups as $group)
        <div class="col">
            <div class="card h-100 shadow-sm border-0 rounded-4 hover-lift">

                <div class="card-body p-4 d-flex flex-column">

                    {{-- Nombre del grupo --}}
                    <h5 class="fw-bold text-dark mb-2 text-truncate">
                        <i class="bi bi-people-fill me-2 text-success"></i>
                        {{ $group->name }}
                    </h5>

                    {{-- Descripción corta --}}
                    <p class="text-muted small mb-3 text-truncate">
                        {{ $group->description ?: 'Sin descripción.' }}
                    </p>

                    {{-- Miembros --}}
                    <p class="small text-muted mb-4">
                        <i class="bi bi-person-fill text-success me-1"></i>
                        {{ count($group->members) }} miembros
                    </p>

                    {{-- Botón de acceso --}}
                    <a href="{{ route('groups.show', $group->group_id) }}"
                        class="btn btn-outline-success rounded-pill w-100 mt-auto">
                        Ver Grupo
                    </a>

                </div>
            </div>
        </div>
        @endforeach

    </div>
    @endif
</div>

{{-- ESTILOS --}}
<style>
    .hover-lift:hover {
        transform: translateY(-5px);
        transition: 0.2s ease;
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, .15) !important;
    }
</style>

@endsection
