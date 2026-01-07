@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold mb-4">Gestión de Usuarios</h1>

    {{-- Mensajes de éxito --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->user_id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->role_id === \App\Models\User::ROLE_ADMIN)
                        Administrador
                    @elseif($user->role_id === \App\Models\User::ROLE_ORGANIZER)
                        Organizador
                    @else
                        Miembro
                    @endif
                </td>
                <td>
                    <a href="{{ route('users.edit', $user->user_id) }}" class="btn btn-sm btn-warning">Editar</a>

                    {{-- Formulario por fila con ruta correcta --}}
                    <form id="delete-user-{{ $user->user_id }}"
                          action="{{ route('users.destroy', $user->user_id) }}"
                          method="POST"
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteUserModal"
                            data-form="delete-user-{{ $user->user_id }}"
                            data-name="{{ $user->name }}"
                            data-expenses="{{ \App\Models\Expense::where('payer_id', $user->user_id)->count() }}"
                            data-unsettled="{{ \App\Models\Expense::where('payer_id', $user->user_id)->where('settled', 0)->count() }}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No hay usuarios registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal: Confirmar eliminación de usuario --}}
<div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="confirmDeleteUserLabel">
                    <i class="bi bi-person-x-fill me-2"></i> Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Eliminar al usuario <span id="deleteUserName" class="fw-bold"></span>?
                <div id="deleteUserWarning" class="mt-2 text-danger fw-bold"></div>
                <span class="text-muted d-block mt-2">Esta acción no se puede deshacer.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button id="confirmDeleteBtn" type="button" class="btn btn-danger rounded-pill">
                    <i class="bi bi-trash me-1"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Script para disparar el form correcto y mostrar advertencia --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const userModal = document.getElementById('confirmDeleteUserModal');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const nameSpan = document.getElementById('deleteUserName');
    const warningSpan = document.getElementById('deleteUserWarning');

    let targetFormId = null;

    userModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        targetFormId = button?.getAttribute('data-form') || null;
        const name = button?.getAttribute('data-name') || '';
        const expenses = parseInt(button?.getAttribute('data-expenses') || 0);
        const unsettled = parseInt(button?.getAttribute('data-unsettled') || 0);

        nameSpan.textContent = name;

        if (expenses > 0) {
            if (unsettled > 0) {
                warningSpan.textContent = `⚠ Este usuario tiene ${unsettled} gastos sin liquidar. Si lo eliminas, también se eliminarán estos gastos.`;
            } else {
                warningSpan.textContent = `⚠ Este usuario tiene ${expenses} gastos liquidados. Si lo eliminas, también se eliminarán.`;
            }
        } else {
            warningSpan.textContent = '';
        }
    });

    confirmBtn.addEventListener('click', () => {
        if (!targetFormId) return;
        const form = document.getElementById(targetFormId);
        if (form) form.submit();
    });
});
</script>

@endsection
