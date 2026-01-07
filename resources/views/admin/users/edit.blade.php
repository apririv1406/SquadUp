@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold mb-4">Editar Usuario</h1>

    <form action="{{ route('users.update', $user->user_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Nombre</label>
            <input type="text" name="name" id="name" class="form-control"
                value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-bold">Email</label>
            <input type="email" name="email" id="email" class="form-control"
                value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label fw-bold">Rol</label>
            <select name="role_id" id="role_id" class="form-select" required>
                <option value="{{ \App\Models\User::ROLE_ADMIN }}"
                    @selected($user->role_id === \App\Models\User::ROLE_ADMIN)>Administrador</option>
                <option value="{{ \App\Models\User::ROLE_ORGANIZER }}"
                    @selected($user->role_id === \App\Models\User::ROLE_ORGANIZER)>Organizador</option>
                <option value="{{ \App\Models\User::ROLE_MEMBER }}"
                    @selected($user->role_id === \App\Models\User::ROLE_MEMBER)>Miembro</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
