@extends('layouts.app')

@section('title', 'Crear Grupo')

@section('content')
<div class="container my-5">

    <h1 class="fw-bold mb-4 text-dark">Crear Nuevo Grupo</h1>

    <form action="{{ route('groups.store') }}" method="POST" class="card shadow-sm p-4 rounded-4 border-0">
        @csrf

        {{-- NOMBRE --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Nombre del grupo</label>
            <input type="text" name="name" class="form-control rounded-3" required>
        </div>

        {{-- DESCRIPCIÓN --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Descripción (opcional)</label>
            <textarea name="description" class="form-control rounded-3" rows="3"></textarea>
        </div>

        {{-- CÓDIGO DE INVITACIÓN --}}
        <div class="mb-4">
            <label class="form-label fw-bold">Código de invitación</label>

            <div class="input-group">
                <input type="text" name="invitation_code" id="invitation_code"
                       class="form-control rounded-start-3"
                       placeholder="Introduce un código o genera uno"
                       maxlength="12">

                <button type="button"
                        class="btn btn-success fw-bold rounded-end-3"
                        onclick="generateCode()">
                    Generar
                </button>
            </div>

            <small class="text-muted">
                Puedes escribir tu propio código o generar uno aleatorio.
            </small>
        </div>

        {{-- BOTÓN CREAR --}}
        <button type="submit"
                class="btn fw-bold px-4 py-2 rounded-pill shadow-sm"
                style="background-color:#2E7D32; color:white; border:2px solid #4CAF50;">
            <i class="bi bi-people-fill me-1"></i> Crear Grupo
        </button>

    </form>
</div>

{{-- SCRIPT PARA GENERAR CÓDIGO --}}
<script>
    function generateCode() {
        const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        let code = "";

        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }

        document.getElementById('invitation_code').value = code;
    }
</script>

@endsection
