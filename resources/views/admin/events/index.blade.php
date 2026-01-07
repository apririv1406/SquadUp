@extends('layouts.app')

@section('title', 'Gestión de Eventos')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold mb-4">Gestión de Eventos</h1>

    {{-- Mensajes de éxito --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Grupo</th>
                <th>Fecha</th>
                <th>Aforo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
            <tr>
                <td>{{ $event->event_id }}</td>
                <td>{{ $event->title }}</td>
                <td>{{ $event->group->name ?? 'Sin grupo' }}</td>
                <td>{{ $event->event_date->format('d/m/Y H:i') }}</td>
                <td>{{ $event->capacity ?? 'Ilimitado' }}</td>
                <td>
                    <a href="{{ route('events.edit', $event->event_id) }}" class="btn btn-sm btn-warning">Editar</a>

                    {{-- Formulario por fila con ruta correcta --}}
                    <form id="delete-event-{{ $event->event_id }}"
                        action="{{ route('events.destroy', $event->event_id) }}"
                        method="POST"
                        class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteEventModal"
                            data-form="delete-event-{{ $event->event_id }}"
                            data-title="{{ $event->title }}"
                            data-expenses="{{ \App\Models\Expense::where('event_id', $event->event_id)->count() }}"
                            data-unsettled="{{ \App\Models\Expense::where('event_id', $event->event_id)->where('settled', 0)->count() }}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No hay eventos registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal: Confirmar eliminación de evento --}}
<div class="modal fade" id="confirmDeleteEventModal" tabindex="-1" aria-labelledby="confirmDeleteEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="confirmDeleteEventLabel">
                    <i class="bi bi-calendar-x-fill me-2"></i> Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Eliminar el evento <span id="deleteEventTitle" class="fw-bold"></span>?
                <div id="deleteEventWarning" class="mt-2 text-danger fw-bold"></div>
                <span class="text-muted d-block mt-2">Esta acción no se puede deshacer.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button id="confirmDeleteEventBtn" type="button" class="btn btn-danger rounded-pill">
                    <i class="bi bi-trash me-1"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Script para disparar el form correcto y mostrar advertencia --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const eventModal = document.getElementById('confirmDeleteEventModal');
        const confirmBtn = document.getElementById('confirmDeleteEventBtn');
        const titleSpan = document.getElementById('deleteEventTitle');
        const warningSpan = document.getElementById('deleteEventWarning');

        let targetFormId = null;

        eventModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            targetFormId = button?.getAttribute('data-form') || null;
            const title = button?.getAttribute('data-title') || '';
            const expenses = parseInt(button?.getAttribute('data-expenses') || 0);
            const unsettled = parseInt(button?.getAttribute('data-unsettled') || 0);

            titleSpan.textContent = title;

            if (expenses > 0) {
                if (unsettled > 0) {
                    warningSpan.textContent = `⚠ Este evento tiene ${unsettled} gastos sin liquidar. Si lo eliminas, también se eliminarán estos gastos.`;
                } else {
                    warningSpan.textContent = `⚠ Este evento tiene ${expenses} gastos liquidados. Si lo eliminas, también se eliminarán.`;
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
