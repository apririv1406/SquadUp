@extends('layouts.app')

@section('title', 'Crear Evento')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold mb-4">Crear Nuevo Evento</h1>

    <form action="{{ route('event.store') }}" method="POST" class="card shadow-sm p-4 rounded-3">
        @csrf

        {{-- SELECCIÓN DE GRUPO --}}
        @if(isset($group) && $group)
            {{-- Si viene desde un grupo --}}
            <input type="hidden" name="group_id" value="{{ $group->group_id }}">

            <div class="alert alert-success rounded-3 mb-4">
                <i class="bi bi-people-fill me-2"></i>
                Creando evento para el grupo:
                <strong>{{ $group->name }}</strong>
            </div>
        @else
            {{-- Si viene desde el dashboard --}}
            <div class="mb-3">
                <label for="group_id" class="form-label fw-bold">Selecciona un Grupo</label>
                <select name="group_id" id="group_id" class="form-select" required>
                    <option value="" disabled selected>Elige un grupo</option>
                    @foreach(auth()->user()->groups as $g)
                        <option value="{{ $g->group_id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- TÍTULO --}}
        <div class="mb-3">
            <label for="title" class="form-label fw-bold">Título del Evento</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        {{-- DEPORTE --}}
        <div class="mb-3">
            <label for="sport" class="form-label fw-bold">Deporte</label>
            <select name="sport" id="sport" class="form-select" required>
                <option value="" disabled selected>Selecciona un deporte</option>
                @foreach($availableSports as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- UBICACIÓN --}}
        <div class="mb-3">
            <label for="location" class="form-label fw-bold">Ubicación</label>
            <input type="text" name="location" id="location" class="form-control" required>
        </div>

        {{-- FECHA --}}
        <div class="mb-3">
            <label for="event_date" class="form-label fw-bold">Fecha y Hora</label>
            <input type="datetime-local" name="event_date" id="event_date" class="form-control" required>
        </div>

        {{-- CAPACIDAD --}}
        <div class="mb-3">
            <label for="capacity" class="form-label fw-bold">Capacidad (opcional)</label>
            <input type="number" name="capacity" id="capacity" class="form-control" min="1">
        </div>

        {{-- PÚBLICO --}}
        <div class="form-check mb-3">
            <input type="checkbox" name="is_public" id="is_public" class="form-check-input" checked>
            <label for="is_public" class="form-check-label">Evento público</label>
        </div>

        {{-- BOTÓN --}}
        <button type="submit" class="btn btn-success fw-bold rounded-pill">
            <i class="bi bi-calendar-plus me-1"></i> Crear Evento
        </button>
    </form>
</div>
@endsection
