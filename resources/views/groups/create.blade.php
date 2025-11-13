@extends('layouts.app')

@section('title', 'Crear Nuevo Grupo')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-8">

            {{-- Título y Contexto --}}
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-people-fill text-custom-green me-3" style="font-size: 2.5rem;"></i>
                <div>
                    <h1 class="h3 fw-bold mb-0">Crear Nuevo Colectivo Deportivo</h1>
                    <p class="text-muted mb-0 small">Define la identidad de tu grupo y comienza a organizar eventos.</p>
                </div>
            </div>

            {{-- Tarjeta del Formulario --}}
            <div class="card shadow-lg border-0" style="border-radius: 1rem;">
                <div class="card-header bg-custom-green-light border-0" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                    <h5 class="mb-0 text-white fw-bold">Información Básica del Grupo</h5>
                </div>
                <div class="card-body p-4 p-md-5">

                    {{-- Formulario de Creación --}}
                    {{-- La acción apunta al método store del GroupController --}}
                    <form method="POST" action="{{ route('groups.store') }}">
                        @csrf

                        {{-- 1. Nombre del Grupo --}}
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-muted">Nombre del Grupo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                   class="form-control rounded-pill @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required autofocus maxlength="100"
                                   placeholder="Ej: Los Invencibles de Baloncesto">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. Descripción --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-muted">Descripción (Opcional)</label>
                            <textarea name="description" id="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="4" placeholder="Breve descripción de la misión, objetivos o tipo de actividades del grupo.">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nota de Rol --}}
                        <div class="alert alert-info border-0 rounded-3 small mt-4" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Como creador, serás automáticamente asignado como el **Organizador** de este grupo.
                        </div>

                        {{-- Botón de Creación --}}
                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-custom-green rounded-pill fw-bold py-3">
                                <i class="bi bi-check-circle-fill me-2"></i> Crear Grupo
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('dashboard') }}" class="small text-muted">Volver al Dashboard</a>
                        </div>
                    </form>

                </div>
            </div> {{-- Fin Card --}}
        </div>
    </div>
</div>
@endsection
