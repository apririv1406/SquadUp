@extends('layouts.app')

@section('content')
<style>
    /* Estilos Personalizados - Replicados desde la vista show */
    :root {
        --custom-green: #198754; /* Lima */
        --custom-green-hover: #157347;
    }
    .custom-border-green {
        border-color: var(--custom-green) !important;
    }
    .custom-text-green {
        color: var(--custom-green) !important;
    }
    .btn-custom-green {
        background-color: var(--custom-green);
        color: white;
        border-color: var(--custom-green);
    }
    .btn-custom-green:hover {
        background-color: var(--custom-green-hover);
        border-color: var(--custom-green-hover);
        color: white;
    }
</style>

<div class="container py-5">

    <!-- Tarjeta Principal de Edición (Misma estética que la vista show) -->
    <div class="card shadow-lg mb-4 border-top border-5 custom-border-green rounded-3">
        <div class="card-body p-4 p-md-5">

            <!-- 1. Título de la Sección -->
            <header class="mb-5">
                <h1 class="card-title h2 fw-bold text-dark mb-1">
                    Administrar Evento
                </h1>
                <p class="custom-text-green fs-5 fw-medium">
                    Editando: {{ $event->title }}
                </p>
                <!-- Botón de Eliminar Evento (Abre Modal de Confirmación) -->
                <button type="button" class="btn btn-outline-danger fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                    <i class="bi bi-trash-fill me-1"></i> Eliminar
                </button>
            </header>

            <!-- 2. Formulario de Edición -->
            <form method="POST" action="{{ route('event.update', $event) }}" class="needs-validation" novalidate>
                @csrf
                <!-- Es fundamental usar @method('PUT') o @method('PATCH') para las actualizaciones en Laravel -->
                @method('PUT')

                <div class="row g-4">

                    <!-- Campo 1: Título del Evento -->
                    <div class="col-md-12">
                        <label for="title" class="form-label fw-bold">Título del Evento</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $event->title) }}" required
                               placeholder="Ej: Torneo Pádel Abierto">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campo 2: Ubicación -->
                    <div class="col-md-6">
                        <label for="location" class="form-label fw-bold">Ubicación</label>
                        <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror"
                               value="{{ old('location', $event->location) }}" required
                               placeholder="Ej: Club Bonasport">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campo 3: Deporte (Desplegable con preselección) -->
                    <div class="col-md-6">
                        <label for="sport_name" class="form-label fw-bold">Deporte</label>
                        <select name="sport_name" id="sport_name" class="form-select @error('sport_name') is-invalid @enderror" required>
                            <option value="" disabled>Selecciona un Deporte</option>
                            @foreach($sportsList as $slug => $name)
                                <option value="{{ $slug }}"
                                    {{ old('sport_name', $event->sport_name) == $slug ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                         @error('sport_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campo 4: Fecha y Hora -->
                    <div class="col-md-6">
                        <label for="event_date" class="form-label fw-bold">Fecha y Hora</label>
                        <!-- Formateamos la fecha para que funcione con input type="datetime-local" -->
                        <input type="datetime-local" name="event_date" id="event_date" class="form-control @error('event_date') is-invalid @enderror"
                               value="{{ old('event_date', \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i')) }}" required>
                        @error('event_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campo 5: Capacidad -->
                    <div class="col-md-6">
                        <label for="capacity" class="form-label fw-bold">Capacidad Máxima</label>
                        <input type="number" name="capacity" id="capacity" class="form-control @error('capacity') is-invalid @enderror"
                               value="{{ old('capacity', $event->capacity) }}" required min="1"
                               placeholder="Máx. participantes">
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campo 6: Visibilidad (is_public) -->
                    <div class="col-md-12">
                        <div class="form-check form-switch p-3 bg-light rounded-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_public" name="is_public"
                                value="1" {{ old('is_public', $event->is_public) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_public">
                                Evento Público (Visible para todos)
                            </label>
                            <div class="text-muted small">
                                Desactiva esta opción para limitar la visibilidad a tu grupo o amigos.
                            </div>
                        </div>
                        @error('is_public')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 3. Botones de Acción -->
                    <div class="col-12 mt-5 border-top pt-4 d-flex justify-content-between">

                        <!-- Botón Principal: Guardar Cambios (Lima) -->
                        <button type="submit" class="btn btn-custom-green btn-lg fw-bold shadow-sm rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> Guardar Cambios
                        </button>

                        <!-- Botón Secundario: Cancelar/Volver -->
                        <a href="{{ route('event.show', $event) }}" class="btn btn-outline-secondary btn-lg fw-bold rounded-pill">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteEventModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>Estás a punto de eliminar el evento **"{{ $event->title }}"** de forma permanente.</p>
                <p class="fw-bold">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('event.destroy', $event) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold rounded-pill">
                        <i class="bi bi-trash me-1"></i> Eliminar Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
