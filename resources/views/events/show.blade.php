{{-- resources/views/events/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalle del Evento')

@section('content')
<style>
    :root {
        --custom-green: #198754;
        --custom-green-hover: #157347;
        --custom-red: #dc3545;
        --custom-red-hover: #c82333;
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

    .btn-custom-red {
        background-color: var(--custom-red);
        color: white;
        border-color: var(--custom-red);
    }

    .btn-custom-red:hover {
        background-color: var(--custom-red-hover);
        border-color: var(--custom-red-hover);
        color: white;
    }

    .info-card-height {
        height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* Estabilizadores para evitar saltos visuales al cargar JS */
    #attendance-button {
        min-width: 260px;
        min-height: 44px;
    }

    #attendance-status-box {
        transition: opacity 150ms ease;
    }
</style>

<div class="container py-5">

    {{-- Mensajes de sesión --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif
    @if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    {{-- Tarjeta Principal del Evento --}}
    <div class="card shadow-lg mb-4 border-top border-5 custom-border-green rounded-3 hover-lift">
        <div class="card-body p-4 p-md-5">
            {{-- Cabecera: título y subtítulo --}}
            <header class="mb-4">
                <h1 class="card-title h2 fw-bold text-dark mb-1">{{ $event->title }}</h1>
                <p class="custom-text-green fs-5 fw-medium">{{ ucfirst($event->sport_name) }} en {{ $event->location }}</p>
            </header>

            {{-- Mensaje de confirmación si asiste (renderizado en servidor para evitar salto) --}}
            <div id="attendance-status-box"
                class="alert alert-success d-flex align-items-center mb-4 {{ $isAttending ? '' : 'd-none' }}"
                role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <span>Ya has confirmado tu asistencia.</span>
            </div>

            {{-- Información clave del evento --}}
            <div class="row text-center border-top pt-4">
                {{-- Fecha y hora --}}
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="bg-light p-3 rounded-3 info-card-height">
                        <p class="text-muted small mb-1">Fecha y Hora</p>
                        <p class="h5 fw-bold text-dark mb-0">{{ \Carbon\Carbon::parse($event->event_date)->format('d M, Y') }}</p>
                        <p class="text-secondary small mb-0">{{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} h</p>
                    </div>
                </div>
                {{-- Capacidad --}}
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="bg-light p-3 rounded-3 info-card-height">
                        <p class="text-muted small mb-1">Capacidad</p>
                        <p class="h5 fw-bold text-dark mb-0">
                            <span id="attendees-count">{{ $confirmedAttendees->count() }}</span> /
                            {{ $event->capacity > 0 ? $event->capacity : '∞' }}
                        </p>
                    </div>
                </div>
                {{-- Creador --}}
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded-3 info-card-height">
                        <p class="text-muted small mb-1">Creador</p>
                        <p class="h5 fw-bold text-dark mb-0">{{ $event->creator->name ?? 'Usuario Desconocido' }}</p>
                    </div>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="mt-4 pt-4 border-top d-flex flex-column flex-md-row justify-content-start gap-3">
                @auth
                @if(!$event->isExpired())
                @if ($isOrganizer)
                <a href="{{ route('event.edit', $event) }}"
                    class="btn btn-sm btn-outline-secondary fw-bold rounded-pill d-flex align-items-center justify-content-center">
                    <i class="bi bi-gear-fill me-1"></i> Administrar Evento
                </a>
                @endif
                @else
                <span class="btn btn-sm btn-outline-secondary fw-bold rounded-pill d-flex align-items-center justify-content-center disabled">
                    <i class="bi bi-gear-fill me-1"></i> Administrar Evento
                </span>
                @endif

                {{-- Botón dinámico de asistencia --}}
                @if(!$event->isExpired())
                <button id="attendance-button"
                    data-event-id="{{ $event->event_id }}"
                    class="btn btn-lg fw-bold shadow-sm rounded-pill d-flex align-items-center justify-content-center {{ $isAttending ? 'btn-custom-red' : 'btn-custom-green' }}"
                    type="button">
                    @if ($isAttending)
                    <i class="bi bi-x-circle me-1"></i> Salir del Evento
                    @else
                    <i class="bi bi-person-add me-1"></i> Confirmar asistencia
                    @endif
                </button>
                @else
                <button class="btn btn-secondary btn-lg fw-bold shadow-sm rounded-pill d-flex align-items-center justify-content-center" type="button" disabled>
                    <i class="bi bi-calendar-x me-1"></i> Evento vencido
                </button>
                @endif
                @endauth

                @guest
                <a href="{{ route('login') }}" class="btn btn-custom-green btn-lg fw-bold shadow-sm rounded-pill">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Inicia Sesión para Unirte
                </a>
                @endguest
            </div>

        </div>
    </div>

    {{-- Participantes confirmados --}}
    <div class="card shadow-sm mb-4 rounded-3">
        <div class="card-body">
            <h2 class="h4 fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-people-fill me-2 custom-text-green"></i>
                Participantes Confirmados (<span id="attendees-count-participants">{{ $confirmedAttendees->count() }}</span>)
            </h2>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3" id="attendees-list-container">
                @forelse ($confirmedAttendees as $attendee)
                <div class="col attendee-item" data-user-id="{{ $attendee->user_id }}">
                    <div class="p-2 bg-light rounded-3 d-flex align-items-center">
                        <div class="me-3 d-flex align-items-center justify-content-center btn-custom-green rounded-circle text-white"
                            style="width: 36px; height: 36px; font-size: 0.9rem;">
                            {{ strtoupper(substr($attendee->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="mb-0 fw-bold text-truncate">{{ $attendee->name }}</p>
                            <p class="mb-0 small text-success">Confirmado</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12" id="no-attendees-message">
                    <p class="text-muted fst-italic">Aún no hay participantes confirmados para este evento.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif
    {{-- Gastos y liquidación --}}
    <div class="row">
        <div class="col-lg-12 mb-4">
            @if ($isAttending || $isOrganizer)
            {{-- Gastos --}}
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h2 class="h4 fw-bold text-dark">
                            <i class="bi bi-cash-stack me-2 custom-text-green"></i> Gastos del Evento
                        </h2>
                        <button type="button" class="btn btn-sm btn-info fw-bold rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                            <i class="bi bi-plus me-1"></i> Añadir Gasto
                        </button>
                    </div>

                    {{-- Lista de gastos --}}
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Pagado por</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Cantidad (€)</th>
                                    <th class="text-muted small">Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="expenses-list-body">
                                @forelse ($eventExpenses as $exp)
                                <tr data-expense-id="{{ $exp->id }}">
                                    <td class="ps-3 fw-semibold">{{ $exp->payer->name ?? 'Usuario Desconocido' }}</td>

                                    {{-- Mostrar descripción completa en tooltip y truncada en la tabla --}}
                                    <td title="{{ $exp->description }}">
                                        {{ \Illuminate\Support\Str::limit($exp->description, 80) }}
                                    </td>

                                    <td class="text-end text-danger">-{{ number_format($exp->amount, 2) }} €</td>

                                    {{-- created_at debe ser instancia de Carbon; si no, usar Carbon::parse --}}
                                    <td class="text-muted small">
                                        {{ optional($exp->created_at)->format('d M Y H:i') ?? \Carbon\Carbon::parse($exp->created_at)->format('d M Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        @if($exp->settled)
                                        {{-- Si está liquidado, mostrar distintivo y deshabilitar acciones --}}
                                        <span class="badge bg-success">Liquidado</span>
                                        @else
                                        {{-- Solo permitir eliminar si no está liquidado y el usuario es el creador u organizador --}}
                                        @if(Auth::id() == $exp->payer_id || $isOrganizer)
                                        <form action="{{ route('event.expense.destroy', [$event->event_id, $exp->expense_id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-danger rounded-pill"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmDeleteExpenseModal"
                                                data-action="{{ route('event.expense.destroy', [$event->event_id, $exp->expense_id]) }}">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </form>
                                        @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr id="no-expenses-row">
                                    <td colspan="4" class="text-muted fst-italic">Aún no hay gastos registrados para este evento.</td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    {{-- Resumen rápido --}}
                    <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold">Gastos registrados:</span>
                            <span class="text-muted ms-2">{{ $eventExpenses->count() }}</span>
                        </div>
                        <div class="fw-bold fs-5">
                            <span>Total Gastos:</span>
                            <span class="text-danger ms-2">-{{ number_format($totalExpenses ?? 0, 2) }} €</span>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-secondary" role="alert">
                Únete al evento para ver y participar en la gestión de gastos y liquidación.
            </div>
            @endif
        </div>

        @if ($isAttending || $isOrganizer)
        <div class="col-lg-12">
            {{-- Liquidación --}}
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <h2 class="h4 fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-wallet-fill me-2 custom-text-green"></i> Liquidación y Saldos
                    </h2>

                    {{-- Lista de liquidación sugerida --}}
                    <div class="mt-4">
                        <h3 class="h6 fw-bold">Quién debe a quién</h3>

                        @if(count($settlements) === 0)
                        <p class="text-muted">No hay movimientos necesarios. Todos están equilibrados.</p>
                        @else
                        <ul class="list-group" id="settlements-list">
                            @foreach($settlements as $s)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-danger">{{ $s['from_name'] }}</strong>
                                    <span class="small text-muted"> debe a </span>
                                    <strong class="text-success">{{ $s['to_name'] }}</strong>
                                </div>
                                <div class="fw-bold">-{{ number_format($s['amount'], 2) }} €</div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    {{-- Lista de gastos con opción de liquidar individualmente --}}
                    <div class="mt-4">
                        <h3 class="h6 fw-bold">Gastos individuales</h3>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pagado por</th>
                                        <th>Descripción</th>
                                        <th class="text-end">Cantidad (€)</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($eventExpenses as $exp)
                                    <tr @if($exp->settled) class="table-secondary text-decoration-line-through" @endif>
                                        <td>{{ $exp->payer->name }}</td>
                                        <td>{{ $exp->description }}</td>
                                        <td class="text-end">{{ number_format($exp->amount, 2) }} €</td>
                                        <td class="text-center">
                                            @if($exp->settled)
                                            <span class="badge bg-success">Liquidado</span>
                                            @else
                                            <span class="badge bg-warning">Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(Auth::id() == $exp->payer_id && !$exp->settled)
                                            <form action="{{ route('event.expense.settle', [$event->event_id, $exp->expense_id]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning rounded-pill">
                                                    <i class="bi bi-check2-circle"></i> Liquidar
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endif

        {{-- Modal de confirmación (simple) --}}
        <div class="modal fade" id="settlementModal" tabindex="-1" aria-labelledby="settlementModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="settlementModalLabel">Confirmar Liquidación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">¿Deseas marcar estas operaciones como realizadas? Esto solo registrará que la liquidación se ha completado en el sistema; no procesa pagos.</p>
                        <ul class="mt-3 small text-muted">
                            <li>Se registrará la fecha de liquidación y se podrá añadir un comprobante manualmente si lo deseas.</li>
                            <li>Si prefieres, puedes ejecutar cada pago fuera de la app y luego marcarlo como pagado.</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>


{{-- Modal: Añadir gasto --}}
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="addExpenseModalLabel">
                    <i class="bi bi-receipt me-2 custom-text-green"></i> Registrar Nuevo Gasto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('event.store_expense', $event) }}" id="addExpenseForm">
                    @csrf
                    <div class="mb-3">
                        <label for="payer_id" class="form-label fw-bold">Pagado por:</label>
                        <select class="form-select" id="payer_id" name="payer_id" required>
                            @foreach($confirmedAttendees as $attendee)
                            <option value="{{ $attendee->user_id }}"
                                @if($attendee->user_id == Auth::id()) selected @endif>
                                {{ $attendee->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">Selecciona al participante que hizo el pago.</div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label fw-bold">Cantidad (€):</label>
                        <input type="number" step="0.01" min="0.01" class="form-control"
                            id="amount" name="amount" placeholder="Ej: 15.50" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Descripción:</label>
                        <input type="text" class="form-control" id="description" name="description"
                            placeholder="Ej: Pago de pista de pádel" maxlength="100" required>
                    </div>

                    <div id="expense-form-alert" class="alert alert-danger d-none mt-3" role="alert" aria-live="polite"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="addExpenseForm" id="save-expense-button" class="btn btn-custom-green rounded-pill">
                    <i class="bi bi-plus-circle me-1"></i> Guardar Gasto
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de estado (confirmación/cancelación asistencia) --}}
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center border-0 shadow-lg">
            <div class="modal-body p-5">
                <div id="modal-icon" class="mb-3"></div>
                <h5 class="modal-title fw-bold fs-4 mb-2" id="modal-title"></h5>
                <p class="text-muted" id="modal-body-text"></p>
                <button type="button" id="modal-understood-button" class="btn btn-sm btn-outline-secondary mt-3 rounded-pill" data-bs-dismiss="modal">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Confirmar eliminación de gasto --}}
<div class="modal fade" id="confirmDeleteExpenseModal" tabindex="-1" aria-labelledby="confirmDeleteExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="confirmDeleteExpenseLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que quieres eliminar este gasto?
                <span class="text-muted d-block mt-2">Esta acción no se puede deshacer.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteExpenseForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill">
                        <i class="bi bi-trash me-1"></i> Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- Inyección de datos JSON (no ejecutable) --}}
@php
$eventData = [
'eventId' => $event->event_id,
'isAttending' => (bool) $isAttending,
'attendeeCount' => $confirmedAttendees->count(),
'currentUserId' => Auth::id(),
'userName' => Auth::user()->name ?? 'Tú',
'eventTitle' => $event->title ?? 'este evento',
'csrfToken' => csrf_token(),
];
@endphp

<script type="application/json" id="event-data-json">
    @json($eventData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
</script>


@vite('resources/js/app.js')

@endsection
