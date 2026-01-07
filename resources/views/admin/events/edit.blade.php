@extends('layouts.app')

@section('title', 'Editar Evento')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold mb-4">Editar Evento</h1>

    <form action="{{ route('events.update', $event->event_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label fw-bold">Título</label>
            <input type="text" name="title" id="title" class="form-control"
                value="{{ old('title', $event->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label fw-bold">Ubicación</label>
            <input type="text" name="location" id="location" class="form-control"
                value="{{ old('location', $event->location) }}" required>
        </div>

        <div class="mb-3">
            <label for="event_date" class="form-label fw-bold">Fecha y hora</label>
            <input type="datetime-local" name="event_date" id="event_date" class="form-control"
                value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="mb-3">
            <label for="capacity" class="form-label fw-bold">Aforo máximo</label>
            <input type="number" name="capacity" id="capacity" class="form-control"
                value="{{ old('capacity', $event->capacity) }}">
        </div>

        <div class="mb-3">
            <label for="is_public" class="form-label fw-bold">¿Evento público?</label>
            <select name="is_public" id="is_public" class="form-select">
                <option value="1" @selected($event->is_public)>Sí</option>
                <option value="0" @selected(!$event->is_public)>No</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
