@extends('layouts.app')

@section('title', 'Editar Evento')

@section('content')
<div class="container my-5">

    <h1 class="fw-bold mb-4">Editar Evento</h1>

    {{-- POPUP DE ÉXITO --}}
    @if (session('success'))
        <div id="successPopup"
             class="alert alert-success shadow-sm rounded-3 position-fixed top-0 start-50 translate-middle-x mt-3 px-4 py-3"
             style="z-index: 9999; display: none;">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">

            <form action="{{ route('admin.events.update', $event->event_id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Título --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Título</label>
                    <input type="text" name="title" class="form-control rounded-3"
                           value="{{ old('title', $event->title) }}" required>
                </div>

                {{-- Ubicación --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Ubicación</label>
                    <input type="text" name="location" class="form-control rounded-3"
                           value="{{ old('location', $event->location) }}">
                </div>

                {{-- Fecha --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Fecha y hora</label>
                    <input type="datetime-local" name="event_date" class="form-control rounded-3"
                           value="{{ old('event_date', \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i')) }}"
                           required>
                </div>

                {{-- Capacidad --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Capacidad</label>
                    <input type="number" name="capacity" class="form-control rounded-3"
                           value="{{ old('capacity', $event->capacity) }}" min="1">
                </div>

                {{-- Público / Privado --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Visibilidad</label>
                    <select name="is_public" class="form-select rounded-3">
                        <option value="1" {{ $event->is_public ? 'selected' : '' }}>Público</option>
                        <option value="0" {{ !$event->is_public ? 'selected' : '' }}>Privado</option>
                    </select>
                </div>

                {{-- Deporte --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Deporte</label>
                    <select name="sport_name" class="form-select rounded-3">
                        @foreach ($sportsList as $key => $label)
                            <option value="{{ $key }}" {{ $event->sport_name === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Grupo --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Grupo</label>
                    <select name="group_id" class="form-select rounded-3">
                        @foreach ($groups as $group)
                            <option value="{{ $group->group_id }}"
                                {{ $event->group_id == $group->group_id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botón --}}
                <button type="submit"
                        class="btn fw-bold px-4 py-2 rounded-pill shadow-sm"
                        style="background-color: #2E7D32; color: white; border: 2px solid #4CAF50;">
                    Guardar Cambios
                </button>

            </form>

        </div>
    </div>

</div>

{{-- SCRIPT PARA MOSTRAR POPUP --}}
@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const popup = document.getElementById('successPopup');
        popup.style.display = 'block';

        setTimeout(() => {
            popup.style.opacity = '0';
            popup.style.transition = 'opacity 0.5s ease';

            setTimeout(() => popup.remove(), 500);
        }, 2000);
    });
</script>
@endif

@endsection
