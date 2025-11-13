@extends('layouts.app')

@section('title', 'Crear Nuevo Evento')

@section('content')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">

            {{-- Título y Contexto --}}
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-calendar-event custom-text-orange me-3" style="font-size: 2rem;"></i>
                <div>
                    <h1 class="h3 fw-bold mb-0">Crear Nuevo Evento para "<span class="text-custom-green">{{ $group->name ?? 'Grupo Desconocido' }}</span>"</h1>
                    <p class="text-muted mb-0 small">Organiza una nueva actividad deportiva para tu grupo. {{--  --}}</p>
                </div>
            </div>

            {{-- Tarjeta del Formulario --}}
            <div class="card shadow-lg border-0" style="border-radius: 1rem;">
                <div class="card-header bg-custom-orange-light border-0" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <h5 class="mb-0 text-white fw-bold">Detalles del Evento</h5>
                </div>
                <div class="card-body p-4 p-md-5">

                    <form method="POST" action="{{ route('events.store', ['group' => $group->group_id ?? 1]) }}">
                        @csrf

                        {{-- 1. Título del Evento --}}
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold text-muted">Título del Evento <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title"
                                   class="form-control rounded-pill @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" required autofocus maxlength="150"
                                   placeholder="Ej: Partido de Baloncesto 5vs5">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. Selector de Deporte (LISTA DESPLEGABLE REQUERIDA) --}}
                        <div class="mb-4">
                            <label for="sport_name" class="form-label fw-bold text-muted">Deporte <span class="text-danger">*</span></label>
                            <select name="sport_name" id="sport_name"
                                    class="form-select rounded-pill @error('sport_name') is-invalid @enderror" required>
                                <option value="" disabled selected>Selecciona un deporte</option>
                                {{-- $sports viene del EventController (lista fija) --}}
                                @foreach ($sports as $key => $name)
                                    <option value="{{ $key }}" {{ old('sport_name') == $key ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sport_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- 3. Fecha y Hora --}}
                            <div class="col-md-6 mb-4">
                                <label for="event_date" class="form-label fw-bold text-muted">Fecha y Hora <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="event_date" id="event_date"
                                       class="form-control rounded-pill @error('event_date') is-invalid @enderror"
                                       value="{{ old('event_date') }}" required>
                                @error('event_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 4. Localización --}}
                            <div class="col-md-6 mb-4">
                                <label for="location" class="form-label fw-bold text-muted">Localización <span class="text-danger">*</span></label>
                                <input type="text" name="location" id="location"
                                       class="form-control rounded-pill @error('location') is-invalid @enderror"
                                       value="{{ old('location') }}" required maxlength="255"
                                       placeholder="Ej: Polideportivo Municipal">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- SECCIÓN DE MATCHMAKING (RF7) --}}
                        <hr class="my-5">
                        <h5 class="fw-bold text-dark mb-3">Configuración de Matchmaking (RF7)</h5>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_public" name="is_public"
                                   value="1" {{ old('is_public') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-muted" for="is_public">
                                Permitir Matchmaking (Hacer Evento Público)
                            </label>
                            <div class="form-text">Si está activado, el evento será visible para otros usuarios fuera del grupo para cubrir plazas.</div>
                        </div>

                        {{-- Capacidad (RF13) --}}
                        {{-- CORRECCIÓN: Se utiliza la clase 'd-none' para ocultar/mostrar y evitar la advertencia CSS del linter. --}}
                        <div class="mb-5" id="capacity_field">
                            <label for="capacity" class="form-label fw-bold text-muted">Capacidad Máxima (Cupo total) <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" id="capacity"
                                   class="form-control rounded-pill @error('capacity') is-invalid @enderror"
                                   value="{{ old('capacity', 0) }}" min="2" placeholder="Ej: 10 (para 5vs5)">
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botón de Creación --}}
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-custom-orange rounded-pill fw-bold py-3">
                                <i class="bi bi-send-fill me-2"></i> Crear Evento y Publicar
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('groups.show', $group->group_id ?? 1) }}" class="small text-muted">Cancelar</a>
                        </div>
                    </form>

                </div>
            </div> {{-- Fin Card --}}
        </div>
    </div>
</div>

<script>
    // Lógica JavaScript para mostrar/ocultar el campo de capacidad
    document.addEventListener('DOMContentLoaded', function () {
        const isPublicCheckbox = document.getElementById('is_public');
        const capacityField = document.getElementById('capacity_field');
        const capacityInput = document.getElementById('capacity');

        function toggleCapacity() {
            if (isPublicCheckbox.checked) {
                // Si está marcado, quitamos la clase d-none
                capacityField.classList.remove('d-none');
                capacityInput.setAttribute('required', 'required');
            } else {
                // Si NO está marcado, añadimos la clase d-none
                capacityField.classList.add('d-none');
                capacityInput.removeAttribute('required');
            }
        }

        // Inicializar el estado basado en el valor old('is_public') del checkbox
        // Usamos la comprobación de checked para ver el estado inicial.
        if (!isPublicCheckbox.checked) {
            capacityField.classList.add('d-none');
        } else {
            capacityField.classList.remove('d-none');
        }

        isPublicCheckbox.addEventListener('change', toggleCapacity);

        // Ejecutar una vez al cargar (ya se hace con el bloque if/else de inicialización)
        // La llamada a toggleCapacity() al final ya no es necesaria, pero se mantiene la lógica
        // de inicialización en el bloque if/else superior.
    });
</script>

@endsection
