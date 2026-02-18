@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container my-5">

    <h1 class="fw-bold mb-4">Editar Usuario</h1>

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

            <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nombre --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre</label>
                    <input type="text" name="name" class="form-control rounded-3"
                           value="{{ old('name', $user->name) }}" required>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control rounded-3"
                           value="{{ old('email', $user->email) }}" required>
                </div>

                {{-- Rol (desde la tabla roles) --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Rol</label>
                    <select name="role_id" class="form-select rounded-3">
                        @foreach ($roles as $role)
                            <option value="{{ $role->role_id }}"
                                {{ $user->role_id == $role->role_id ? 'selected' : '' }}>
                                {{ $role->name }}
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
