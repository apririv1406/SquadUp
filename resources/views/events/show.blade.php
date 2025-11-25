<!--
    Vista de detalle del evento (Event::show) - Implementada con Bootstrap 5.
    ACTUALIZACIÓN: Se añade el Modal para añadir gastos y su lógica de alta.
-->

@extends('layouts.app')

@section('content')
<style>
    /* Estilos Personalizados según la estética requerida */
    :root {
        --custom-green: #198754;
        --custom-green-hover: #157347;
        --custom-red: #dc3545; /* Rojo de Bootstrap para cancelar */
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
    /* Clase para altura uniforme de tarjetas de resumen */
    .info-card-height {
        height: 100px; /* Altura fija para uniformidad visual */
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Estilos para el botón de Cancelación */
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

    /* Estilo para el mensaje 'Ya has confirmado' */
    .attendance-status-message {
        /* Eliminamos la transición compleja por ahora,
           ya que usaremos d-none que es instantáneo. */
    }
</style>

<div class="container py-5">

    <!-- Tarjeta Principal del Evento -->
    <div class="card shadow-lg mb-4 border-top border-5 custom-border-green rounded-3">
        <div class="card-body p-4 p-md-5">

            <!-- 1. Título y Subtítulo del Evento -->
            <header class="mb-4">
                <h1 class="card-title h2 fw-bold text-dark mb-1">
                    {{ $event->title }}
                </h1>
                <p class="custom-text-green fs-5 fw-medium">
                    {{ ucfirst($event->sport_name) }} en {{ $event->location }}
                </p>
            </header>

            <!-- 1.1. MENSAJE DE CONFIRMACIÓN (SOLO SI ASISTE) -->
            <div
                id="attendance-status-box"
                class="attendance-status-message alert alert-success d-flex align-items-center mb-4 {{ !$isAttending ? 'd-none' : '' }}"
                role="alert"
            >
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <span id="status-text">Ya has confirmado tu asistencia.</span>
            </div>

            <!-- 2. Información Clave del Evento -->
            <div class="row text-center border-top pt-4">
                <!-- Tarjeta 1: Fecha y Hora -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="bg-light p-3 rounded-3 info-card-height">
                        <p class="text-muted small mb-1">Fecha y Hora</p>
                        <p class="h5 fw-bold text-dark mb-0">{{ \Carbon\Carbon::parse($event->event_date)->format('d M, Y') }}</p>
                        <p class="text-secondary small mb-0">{{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} h</p>
                    </div>
                </div>
                <!-- Tarjeta 2: Capacidad (ACTUALIZAR CON JS AL CONFIRMAR) -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="bg-light p-3 rounded-3 info-card-height">
                        <p class="text-muted small mb-1">Capacidad</p>
                        <p class="h5 fw-bold text-dark mb-0">
                            <span id="attendees-count">{{ $confirmedAttendees->count() }}</span> / {{ $event->capacity }}
                        </p>
                    </div>
                </div>
                <!-- Tarjeta 3: Creador -->
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded-3 info-card-height">
                        <p class="text-muted small mb-1">Creador</p>
                        <p class="h5 fw-bold text-dark mb-0">
                            {{ $event->creator->name ?? 'Usuario Desconocido' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- 3. Botones de Acción -->
            <div class="mt-4 pt-4 border-top d-flex flex-column flex-md-row justify-content-start gap-3">
                @auth
                    @if ($isOrganizer)
                        <!-- Botón para el Creador/Organizador -->
                        <a href="{{ route('event.edit', $event) }}" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill d-flex align-items-center justify-content-center">
                            <i class="bi bi-gear-fill me-1"></i> Administrar Evento
                        </a>
                    @endif

                    <!-- BOTÓN DINÁMICO DE ASISTENCIA -->
                    <button
                        id="attendance-button"
                        data-event-id="{{ $event->event_id }}"
                        class="btn btn-lg fw-bold shadow-sm rounded-pill d-flex align-items-center justify-content-center"
                        type="button"
                    >
                        <!-- El texto y las clases se establecen en el script de JS -->
                    </button>

                @else
                    <!-- Si no está autenticado, mantenemos el enlace de login -->
                    <a href="{{ route('login') }}" class="btn btn-custom-green btn-lg fw-bold shadow-sm rounded-pill">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Inicia Sesión para Unirte
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- ... Sección de Participantes Confirmados ... -->
    <div class="card shadow-sm mb-4 rounded-3">
        <div class="card-body">
            <h2 class="h4 fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-people-fill me-2 custom-text-green"></i> Participantes Confirmados (<span id="attendees-count-participants">{{ $confirmedAttendees->count() }}</span>)
            </h2>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3" id="attendees-list-container">
                @forelse ($confirmedAttendees as $attendee)
                    <div class="col attendee-item" data-user-id="{{ $attendee->user_id }}">
                        <div class="p-2 bg-light rounded-3 d-flex align-items-center">
                            <!-- Avatar con inicial (Fondo Lima) -->
                            <div class="me-3 d-flex align-items-center justify-content-center btn-custom-green rounded-circle text-white" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                {{ strtoupper(substr($attendee->name, 0, 1)) }}
                            </div>
                            <!-- Nombre y Estado -->
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

    <!-- ... Contenido de Gastos y Liquidación ... -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            @if ($isAttending || $isOrganizer)
            <!-- ... Sección de Gastos ... -->
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h2 class="h4 fw-bold text-dark">
                            <i class="bi bi-cash-stack me-2 custom-text-green"></i> Gastos del Evento
                        </h2>
                        <!-- Botón para abrir el modal de añadir gasto -->
                        <button type="button" class="btn btn-sm btn-info fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                            <i class="bi bi-plus me-1"></i> Añadir Gasto
                        </button>
                    </div>
                    <!-- Lista de Gastos (Placeholder) -->
                    <!-- ... -->
                    <div class="pt-3 border-top mt-3 d-flex justify-content-between fw-bold fs-5">
                        <span>Total Gastos:</span>
                        <span class="text-danger">
                            -{{ number_format($expenses, 2) }} €
                        </span>
                    </div>
                </div>
            </div>
            @else
            <!-- Mensaje si no está asistiendo -->
            <div class="alert alert-secondary" role="alert">
                Únete al evento para ver y participar en la gestión de gastos y liquidación.
            </div>
            @endif
        </div>

        @if ($isAttending || $isOrganizer)
        <div class="col-lg-12">
            <!-- ... Sección de Liquidación ... -->
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <h2 class="h4 fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-wallet-fill me-2 custom-text-green"></i> Liquidación y Saldos
                    </h2>
                    <!-- ... Mensaje de Estado de Liquidación (Placeholder) ... -->
                    <button type="button" class="btn btn-custom-green w-100 fw-bold mt-4 shadow-sm rounded-pill">
                        <i class="bi bi-bank me-1"></i> Liquidar Cuentas Ahora
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- ------------------------------------------------------------------------- -->
<!-- NUEVO MODAL PARA AÑADIR GASTO                                             -->
<!-- ------------------------------------------------------------------------- -->
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
                    <!-- Campo Payer ID (Quién pagó) -->
                    <div class="mb-3">
                        <label for="payer_id" class="form-label fw-bold">Pagado por:</label>
                        <select class="form-select" id="payer_id" name="payer_id" required>
                            <!-- Opciones rellenadas por JS -->
                        </select>
                        <div class="form-text">Selecciona al participante que hizo el pago.</div>
                    </div>

                    <!-- Campo Amount (Cantidad) -->
                    <div class="mb-3">
                        <label for="amount" class="form-label fw-bold">Cantidad (€):</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" placeholder="Ej: 15.50" required>
                    </div>

                    <!-- Campo Description (Descripción) -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Descripción:</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="Ej: Pago de pista de pádel" maxlength="100" required>
                    </div>

                    <div id="expense-form-alert" class="alert alert-danger d-none mt-3" role="alert"></div>
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


<!-- MODAL PARA EL POPUP DE CONFIRMACIÓN/CANCELACIÓN (Ya Existente) -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center border-0 shadow-lg">
            <div class="modal-body p-5">
                <div id="modal-icon" class="mb-3">
                    <!-- Icono dinámico -->
                </div>
                <h5 class="modal-title fw-bold fs-4 mb-2" id="modal-title"></h5>
                <p class="text-muted" id="modal-body-text"></p>
                <!-- Botón que activa la recarga de página -->
                <button type="button" id="modal-understood-button" class="btn btn-sm btn-outline-secondary mt-3 rounded-pill" data-bs-dismiss="modal">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ------------------------------------------------------------------------- -->
<!-- PASO 1: INYECCIÓN SEGURA DE DATOS (Aísla PHP para el editor/linter)       -->
<!-- ------------------------------------------------------------------------- -->
<script>
    // Creamos un objeto global (window.EventData) para inyectar datos de Blade/PHP.
    window.EventData = {
        eventId: {!! json_encode($event->event_id ?? null) !!},
        isAttending: {!! json_encode((bool) $isAttending) !!},
        attendeeCount: {!! json_encode($confirmedAttendees->count()) !!},
        currentUserId: {!! json_encode(Auth::id() ?? null) !!},
        userName: {!! json_encode(Auth::user()->name ?? 'Tú') !!},
        eventTitle: {!! json_encode($event->title ?? 'este evento') !!},
        // Añadimos la lista de participantes para el modal de gastos
        attendees: {!! json_encode($confirmedAttendees->map(function ($attendee) {
            return ['id' => $attendee->user_id, 'name' => $attendee->name];
        })) !!},
        csrfToken: document.querySelector('meta[name="csrf-token"]').content,
    };
</script>


<!-- ------------------------------------------------------------------------- -->
<!-- PASO 2: LÓGICA DE JAVASCRIPT (Funciones de Asistencia y Gastos)          -->
<!-- ------------------------------------------------------------------------- -->
<script src="{{ asset('resources/js/expense_handler.js') }}">

        document.addEventListener('DOMContentLoaded', function() {

        // 1. Variables de Estado y Elementos del DOM
        const eventId = window.EventData.eventId;
        let isAttending = window.EventData.isAttending;
        let attendeeCount = window.EventData.attendeeCount;
        const currentUserId = window.EventData.currentUserId;
        const userName = window.EventData.userName;
        const eventTitle = window.EventData.eventTitle;
        const attendees = window.EventData.attendees;
        const csrfToken = window.EventData.csrfToken;

        // Elementos de la vista principal (Asistencia)
        const button = document.getElementById('attendance-button');
        const countDisplay = document.getElementById('attendees-count');
        const countDisplayParticipants = document.getElementById('attendees-count-participants');
        const statusBox = document.getElementById('attendance-status-box');
        const attendeesListContainer = document.getElementById('attendees-list-container');
        const noAttendeesMessage = document.getElementById('no-attendees-message');

        // Modal de Asistencia/Error (StatusModal)
        const statusModalElement = document.getElementById('statusModal');
        const statusModal = new bootstrap.Modal(statusModalElement, {});
        const modalTitle = document.getElementById('modal-title');
        const modalIcon = document.getElementById('modal-icon');
        const modalBodyText = document.getElementById('modal-body-text');

        // Modal de Gasto (ExpenseModal)
        const expenseModalElement = document.getElementById('addExpenseModal');
        const expenseModal = new bootstrap.Modal(expenseModalElement, {});
        const expenseForm = document.getElementById('addExpenseForm');
        const payerSelect = document.getElementById('payer_id');
        const expenseSaveButton = document.getElementById('save-expense-button');
        const expenseFormAlert = document.getElementById('expense-form-alert');


        // =========================================================================
        // A. LÓGICA DE ASISTENCIA (Attendance Logic)
        // =========================================================================

        /** Muestra el modal de confirmación/cancelación */
        function showStatusModal(title, body, type) {
            modalTitle.textContent = title;
            modalBodyText.textContent = body;

            let iconHtml = '';
            let iconColor = '';
            if (type === 'join') {
                iconHtml = '<i class="bi bi-check-circle-fill display-3"></i>';
                iconColor = 'text-success';
            } else if (type === 'leave') {
                iconHtml = '<i class="bi bi-x-circle-fill display-3"></i>';
                iconColor = 'text-danger';
            } else {
                iconHtml = '<i class="bi bi-exclamation-triangle-fill display-3"></i>';
                iconColor = 'text-warning';
            }

            modalIcon.innerHTML = `<span class="${iconColor}">${iconHtml}</span>`;

            statusModal.show();
        }

        /** Actualiza el estado del botón y el mensaje superior */
        function updateUI(attending) {
            isAttending = attending;

            // 1. Limpiar clases de estilo previas y actualizar estado del botón
            button.classList.remove('btn-custom-green', 'btn-custom-red', 'btn-warning');
            button.innerHTML = '';

            if (attending) {
                // Estado: Asistiendo (Botón rojo de Cancelar)
                button.innerHTML = '<i class="bi bi-x-circle me-1"></i> Salir del Evento';
                button.classList.add('btn-custom-red');

                statusBox.classList.remove('d-none');
                statusBox.classList.remove('alert-warning');
                statusBox.classList.add('alert-success');
            } else {
                // Estado: No Asistiendo (Botón verde de Confirmar)
                button.innerHTML = '<i class="bi bi-person-add me-1"></i> Confirmar asistencia';
                button.classList.add('btn-custom-green');

                statusBox.classList.add('d-none');
            }

            // 2. Actualizar contadores
            countDisplay.textContent = attendeeCount;
            countDisplayParticipants.textContent = attendeeCount;

            // 3. Actualizar la lista de participantes (Simple)
            updateAttendeesList(attending);
        }

        /** Actualiza la lista de asistentes (añade/elimina el elemento del usuario actual) */
        function updateAttendeesList(attending) {
            if (!currentUserId) return;

            const userElement = attendeesListContainer.querySelector(`.attendee-item[data-user-id="${currentUserId}"]`);

            // La lógica de añadir/eliminar fue eliminada ya que la recarga de página lo maneja.
            // Mantenemos solo la parte de ocultar el mensaje de no asistentes si es necesario.
            if (attending && attendeesListContainer.querySelectorAll('.attendee-item').length > 0) {
                 const noMessage = document.getElementById('no-attendees-message');
                 if (noMessage) { noMessage.classList.add('d-none'); }
            } else if (!attending && attendeesListContainer.querySelectorAll('.attendee-item').length === 0) {
                 const noMessage = document.getElementById('no-attendees-message');
                 if (noMessage) { noMessage.classList.remove('d-none'); }
            }
        }


        // Función Principal de Lógica AJAX para asistencia
        async function toggleAttendance() {
            if (!currentUserId) return;

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';

            const action = isAttending ? 'leave' : 'join';
            const url = `/events/${eventId}/${action}`;

            let success = false;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ event_id: eventId })
                });

                const data = await response.json();

                if (response.ok) {
                    success = true;

                    const wasAttending = isAttending;
                    const newAttendingStatus = data.attending;

                    if (newAttendingStatus && !wasAttending) {
                        attendeeCount++;
                    } else if (!newAttendingStatus && wasAttending) {
                        attendeeCount--;
                    }

                    updateUI(newAttendingStatus);

                    if (action === 'join') {
                        showStatusModal('¡Asistencia Confirmada!', `Has confirmado correctamente tu asistencia a ${eventTitle}. Pulsa "Entendido" para recargar los datos.`, 'join');
                    } else if (action === 'leave') {
                         showStatusModal('Asistencia Cancelada', `Tu asistencia ha sido cancelada para ${eventTitle}. Pulsa "Entendido" para recargar los datos.`, 'leave');
                    }


                } else if (response.status === 409) {
                    showStatusModal('Atención', data.message || 'El estado no se ha podido actualizar correctamente.', 'warning');

                } else {
                    console.error('Error del servidor:', data.message);
                    showStatusModal('Error', data.message || 'Error desconocido al procesar la solicitud. Intenta recargar la página.', 'error');
                }
            } catch (error) {
                console.error('Error de red o servidor:', error);
                showStatusModal('Error de Conexión', 'Ha ocurrido un error de conexión con el servidor. Por favor, verifica tu conexión a internet.', 'error');
            } finally {
                button.disabled = false;
                if (!success) {
                   updateUI(isAttending);
                }
            }
        }

        // =========================================================================
        // B. LÓGICA DE GASTOS (Expense Logic)
        // =========================================================================

        /** Rellena el select de "Pagado por" con los asistentes. */
        function populatePayerSelect() {
            // Limpiar opciones previas
            payerSelect.innerHTML = '<option value="" disabled selected>Selecciona un pagador</option>';

            // Añadir asistentes
            attendees.forEach(attendee => {
                const option = document.createElement('option');
                option.value = attendee.id;
                option.textContent = attendee.name;

                // Seleccionar al usuario actual por defecto si está presente
                if (attendee.id === currentUserId) {
                    option.selected = true;
                }
                payerSelect.appendChild(option);
            });
        }

        /** Maneja el envío del formulario de gasto. */
        async function handleAddExpense(e) {
            e.preventDefault();

            expenseSaveButton.disabled = true;
            expenseSaveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Guardando...';
            expenseFormAlert.classList.add('d-none');

            const amount = parseFloat(document.getElementById('amount').value);
            const description = document.getElementById('description').value;
            const payerId = document.getElementById('payer_id').value;

            // Simple validación
            if (isNaN(amount) || amount <= 0 || !description || !payerId) {
                expenseFormAlert.textContent = 'Por favor, asegúrate de rellenar todos los campos correctamente.';
                expenseFormAlert.classList.remove('d-none');
                expenseSaveButton.disabled = false;
                expenseSaveButton.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Guardar Gasto';
                return;
            }

            const datosGasto = {
                amount: amount,
                description: description,
                payer_id: payerId
            };

            const url = `/events/${eventId}/expense`; // Usa la URL correcta

            // 1. Obtener el token CSRF (Crucial para peticiones POST en Laravel)
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(url, {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken, // Añadir el token
                        'Accept': 'application/json' // Pedir una respuesta JSON
                    },
                    body: JSON.stringify(datosGasto)
                });

                // Manejar la respuesta del JSON (éxito o error 422/500)
                const result = await response.json();

                if (response.ok) {
                    console.log('Gasto registrado con éxito:', result);
                    // Actualizar la interfaz
                } else {
                    console.error('Error del servidor:', result.message, result.errors);
                    // Mostrar mensaje de error al usuario
                }
            } catch (error) {
                console.error('Error de red o procesamiento:', error);
            } finally {
                // Asegurarse de restaurar el botón y el formulario
                expenseSaveButton.disabled = false;
                expenseSaveButton.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Guardar Gasto';
            }
        }

        // =========================================================================
        // C. INICIALIZACIÓN Y LISTENERS
        // =========================================================================

        // 1. Inicialización de la UI de asistencia
        if (button) {
            updateUI(isAttending);
            button.addEventListener('click', toggleAttendance);
        }

        // 2. Listener para recargar la página al cerrar el StatusModal (asistencia/error)
        statusModalElement.addEventListener('hidden.bs.modal', function () {
            // Solo recargamos si no hay errores fatales
            window.location.reload();
        });

        // 3. Listeners para el Modal de Gastos

        // Rellenar el select de pagadores justo antes de mostrar el modal
        expenseModalElement.addEventListener('show.bs.modal', populatePayerSelect);

        // Limpiar el formulario y las alertas al cerrarse el modal
        expenseModalElement.addEventListener('hidden.bs.modal', function () {
            expenseForm.reset();
            expenseFormAlert.classList.add('d-none');
            expenseSaveButton.disabled = false;
            expenseSaveButton.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Guardar Gasto';
        });

        // Manejar el envío del formulario
        expenseForm.addEventListener('submit', handleAddExpense);
    });

</script>

@endsection
