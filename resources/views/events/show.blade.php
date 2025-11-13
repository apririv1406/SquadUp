<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Detalle del Evento (Show)</title>
    <!-- Incluir Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- El script de Bootstrap JS debe ir al final del <body>, pero lo incluimos aquí para la simulación -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Simulamos el CSRF Token para que el JS no falle al buscarlo -->
    <meta name="csrf-token" content="simulated-csrf-token">
    <style>
        /* Estilos Personalizados según la estética requerida */
        :root {
            --custom-green: #198754;
            --custom-green-hover: #157347;
            --custom-red: #dc3545;
            /* Rojo de Bootstrap para cancelar/salir */
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
            height: 100px;
            /* Altura fija para uniformidad visual */
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
    </style>
</head>

<body>
    <!-- SIMULACIÓN DE VARIABLES PHP/BLADE -->
    <script>
        const SIMULATED_EVENT = {
            event_id: 123,
            title: "Partido de Fútbol Amistoso",
            sport_name: "Fútbol",
            location: "Polideportivo Municipal",
            event_date: "2025-12-15 18:30:00",
            capacity: 10,
            creator: {
                name: "Marco Pérez",
                user_id: 1
            }
        };

        const SIMULATED_ATTENDEES = [{
            user_id: 1,
            name: "Marco Pérez"
        }, {
            user_id: 2,
            name: "Laura Gómez"
        }, {
            user_id: 3,
            name: "Carlos Ruiz"
        }];

        const SIMULATED_USER_ID = 2; // Simula el usuario actual
        const SIMULATED_IS_ATTENDING = SIMULATED_ATTENDEES.some(a => a.user_id === SIMULATED_USER_ID);
        const SIMULATED_IS_ORGANIZER = SIMULATED_EVENT.creator.user_id === SIMULATED_USER_ID;
        const SIMULATED_EXPENSES = 45.00;

        // Función de simulación para reemplazar las llamadas de Carbon y Blade
        const formatDateTime = (date, format) => {
            const d = new Date(date);
            if (format === 'd M, Y') {
                return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/\./g, '');
            }
            if (format === 'H:i') {
                return d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: false });
            }
            return date;
        };

        // Creamos un objeto global (window.EventData) para inyectar datos
        window.EventData = {
            eventId: SIMULATED_EVENT.event_id,
            isAttending: SIMULATED_IS_ATTENDING,
            attendeeCount: SIMULATED_ATTENDEES.length,
            currentUserId: SIMULATED_USER_ID,
            userName: SIMULATED_USER_ID === SIMULATED_EVENT.creator.user_id ? SIMULATED_EVENT.creator.name : 'Tú',
            eventTitle: SIMULATED_EVENT.title,
            initialExpenses: SIMULATED_EXPENSES,
            attendees: SIMULATED_ATTENDEES.map(attendee => ({
                id: attendee.user_id,
                name: attendee.name
            })),
            csrfToken: document.querySelector('meta[name="csrf-token"]').content,
            expenseUrl: `/events/${SIMULATED_EVENT.event_id}/expense`,
            joinUrl: `/events/${SIMULATED_EVENT.event_id}/join`,
            leaveUrl: `/events/${SIMULATED_EVENT.event_id}/leave`,
        };

        // Simulación de funciones JS de asistencia y gastos
        function updateAttendanceUI(isAttending) {
            const btn = document.getElementById('attendance-button');
            const statusBox = document.getElementById('attendance-status-box');

            if (isAttending) {
                btn.classList.remove('btn-custom-green');
                btn.classList.add('btn-custom-red');
                btn.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Cancelar Asistencia';
                statusBox.classList.remove('d-none');
            } else {
                btn.classList.remove('btn-custom-red');
                btn.classList.add('btn-custom-green');
                btn.innerHTML = '<i class="bi bi-person-check-fill me-1"></i> Confirmar Asistencia';
                statusBox.classList.add('d-none');
            }
        }

        // Simulación de carga inicial
        window.addEventListener('load', () => {
            updateAttendanceUI(window.EventData.isAttending);
        });

        // SIMULACIÓN DE MÓDULOS DE ASISTENCIA Y GASTOS (NO FUNCIONALES AQUÍ, SÓLO SIMULACIÓN DE UI)
        document.addEventListener('DOMContentLoaded', () => {
             // Simulación de la función del botón de asistencia (simula el toggle)
            const btn = document.getElementById('attendance-button');
            if (btn) {
                btn.addEventListener('click', () => {
                    const isAttendingNow = !window.EventData.isAttending;
                    window.EventData.isAttending = isAttendingNow;
                    updateAttendanceUI(isAttendingNow);

                    // Simulación de actualización de contador
                    const countEl = document.getElementById('attendees-count');
                    const countParticipantsEl = document.getElementById('attendees-count-participants');
                    let currentCount = parseInt(countEl.textContent.split('/')[0].trim());

                    if (isAttendingNow) {
                        currentCount += 1;
                    } else {
                        currentCount -= 1;
                    }

                    countEl.textContent = `${currentCount} / ${SIMULATED_EVENT.capacity}`;
                    countParticipantsEl.textContent = currentCount;

                    // Mostrar modal de estado (simulación)
                    const modalTitle = document.getElementById('modal-title');
                    const modalBodyText = document.getElementById('modal-body-text');
                    const modalIcon = document.getElementById('modal-icon');

                    if (isAttendingNow) {
                        modalTitle.textContent = "¡Asistencia Confirmada!";
                        modalBodyText.textContent = `Te has unido a ${SIMULATED_EVENT.title}. ¡Nos vemos allí!`;
                        modalIcon.innerHTML = `<i class="bi bi-calendar-check text-success display-4"></i>`;
                    } else {
                        modalTitle.textContent = "Asistencia Cancelada";
                        modalBodyText.textContent = `Has cancelado tu asistencia a ${SIMULATED_EVENT.title}. Lamentamos que no puedas venir.`;
                        modalIcon.innerHTML = `<i class="bi bi-calendar-x text-danger display-4"></i>`;
                    }

                    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
                    statusModal.show();
                });
            }

            // Simulación de la función del modal de gastos
            const addExpenseForm = document.getElementById('addExpenseForm');
            if (addExpenseForm) {
                addExpenseForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    // Aquí iría la lógica de envío AJAX

                    // Cierra el modal de gastos y muestra el modal de estado
                    const expenseModal = bootstrap.Modal.getInstance(document.getElementById('addExpenseModal'));
                    expenseModal.hide();

                    const modalTitle = document.getElementById('modal-title');
                    const modalBodyText = document.getElementById('modal-body-text');
                    const modalIcon = document.getElementById('modal-icon');

                    modalTitle.textContent = "Gasto Registrado";
                    modalBodyText.textContent = `El gasto ha sido guardado exitosamente y será considerado en la liquidación.`;
                    modalIcon.innerHTML = `<i class="bi bi-cash-coin text-info display-4"></i>`;

                    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
                    statusModal.show();

                    // Simulación de actualización de total
                    const amount = parseFloat(document.getElementById('amount').value) || 0;
                    window.EventData.initialExpenses += amount;
                    document.getElementById('total-expenses-display').textContent = `-${window.EventData.initialExpenses.toFixed(2)} €`;

                    addExpenseForm.reset(); // Limpiar formulario
                });
            }
        });
    </script>

    <!-- COMIENZA EL CONTENIDO DE LA VISTA (dentro de @section('content')) -->
    <div class="container py-5">

        <!-- Tarjeta Principal del Evento -->
        <div class="card shadow-lg mb-4 border-top border-5 custom-border-green rounded-3">
            <div class="card-body p-4 p-md-5">

                <!-- 1. Título y Subtítulo del Evento -->
                <header class="mb-4">
                    <h1 class="card-title h2 fw-bold text-dark mb-1">
                        <!-- SIMULADO: {{ $event->title }} -->
                        {{ SIMULATED_EVENT.title }}
                    </h1>
                    <p class="custom-text-green fs-5 fw-medium">
                        <!-- SIMULADO: {{ ucfirst($event->sport_name) }} en {{ $event->location }} -->
                        {{ SIMULATED_EVENT.sport_name }} en {{ SIMULATED_EVENT.location }}
                    </p>
                </header>

                <!-- 1.1. MENSAJE DE CONFIRMACIÓN (SOLO SI ASISTE) -->
                <div id="attendance-status-box"
                    class="attendance-status-message alert alert-success d-flex align-items-center mb-4 {{ !SIMULATED_IS_ATTENDING ? 'd-none' : '' }}"
                    role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <span id="status-text">Ya has confirmado tu asistencia.</span>
                </div>

                <!-- 2. Información Clave del Evento -->
                <div class="row text-center border-top pt-4">
                    <!-- Tarjeta 1: Fecha y Hora -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="bg-light p-3 rounded-3 info-card-height">
                            <p class="text-muted small mb-1">Fecha y Hora</p>
                            <p class="h5 fw-bold text-dark mb-0" id="event-date-display">
                                <!-- SIMULADO: {{ \Carbon\Carbon::parse($event->event_date)->format('d M, Y') }} -->
                                {{ formatDateTime(SIMULATED_EVENT.event_date, 'd M, Y') }}
                            </p>
                            <p class="text-secondary small mb-0" id="event-time-display">
                                <!-- SIMULADO: {{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} h -->
                                {{ formatDateTime(SIMULATED_EVENT.event_date, 'H:i') }} h
                            </p>
                        </div>
                    </div>
                    <!-- Tarjeta 2: Capacidad (ACTUALIZAR CON JS AL CONFIRMAR) -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="bg-light p-3 rounded-3 info-card-height">
                            <p class="text-muted small mb-1">Capacidad</p>
                            <p class="h5 fw-bold text-dark mb-0">
                                <span id="attendees-count">{{ SIMULATED_ATTENDEES.length }}</span> / {{ SIMULATED_EVENT.capacity }}
                            </p>
                        </div>
                    </div>
                    <!-- Tarjeta 3: Creador -->
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded-3 info-card-height">
                            <p class="text-muted small mb-1">Creador</p>
                            <p class="h5 fw-bold text-dark mb-0">
                                <!-- SIMULADO: {{ $event->creator->name ?? 'Usuario Desconocido' }} -->
                                {{ SIMULATED_EVENT.creator.name }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 3. Botones de Acción -->
                <div class="mt-4 pt-4 border-top d-flex flex-column flex-md-row justify-content-start gap-3">
                    <!-- SIMULACIÓN @auth -->
                    <!-- Simulación de autenticación: Si el usuario está "logueado" (SIMULATED_USER_ID != null) -->
                    <script>
                        if (SIMULATED_USER_ID !== null) {
                            if (SIMULATED_IS_ORGANIZER) {
                                document.write(`
                                    <!-- Botón para el Creador/Organizador -->
                                    <a href="#" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill d-flex align-items-center justify-content-center">
                                        <i class="bi bi-gear-fill me-1"></i> Administrar Evento
                                    </a>
                                `);
                            }

                            document.write(`
                                <!-- BOTÓN DINÁMICO DE ASISTENCIA (se actualiza por JS) -->
                                <button
                                    id="attendance-button"
                                    data-event-id="${SIMULATED_EVENT.event_id}"
                                    class="btn btn-lg fw-bold shadow-sm rounded-pill d-flex align-items-center justify-content-center"
                                    type="button"
                                >
                                    <!-- El texto y las clases se establecen en JS al cargar -->
                                </button>
                            `);
                        } else {
                            document.write(`
                                <!-- Si no está autenticado, mantenemos el enlace de login -->
                                <a href="#" class="btn btn-custom-green btn-lg fw-bold shadow-sm rounded-pill">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Inicia Sesión para Unirte
                                </a>
                            `);
                        }
                    </script>
                    <!-- FIN SIMULACIÓN @auth -->
                </div>
            </div>
        </div>

        <!-- Sección de Participantes Confirmados -->
        <div class="card shadow-sm mb-4 rounded-3">
            <div class="card-body">
                <h2 class="h4 fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="bi bi-people-fill me-2 custom-text-green"></i> Participantes Confirmados (<span id="attendees-count-participants">{{ SIMULATED_ATTENDEES.length }}</span>)
                </h2>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3" id="attendees-list-container">
                    <!-- SIMULACIÓN @forelse ($confirmedAttendees as $attendee) -->
                    <script>
                        const attendeesListContainer = document.getElementById('attendees-list-container');
                        if (SIMULATED_ATTENDEES.length > 0) {
                            SIMULATED_ATTENDEES.forEach(attendee => {
                                const isCurrentUser = attendee.user_id === SIMULATED_USER_ID;
                                const initials = attendee.name.substring(0, 1).toUpperCase();

                                const attendeeHtml = `
                                    <div class="col attendee-item" data-user-id="${attendee.user_id}">
                                        <div class="p-2 bg-light rounded-3 d-flex align-items-center ${isCurrentUser ? 'border border-primary' : ''}">
                                            <!-- Avatar con inicial (Fondo Lima) -->
                                            <div class="me-3 d-flex align-items-center justify-content-center btn-custom-green rounded-circle text-white" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                                ${initials}
                                            </div>
                                            <!-- Nombre y Estado -->
                                            <div>
                                                <p class="mb-0 fw-bold text-truncate">${attendee.name} ${isCurrentUser ? '(Tú)' : ''}</p>
                                                <p class="mb-0 small text-success">Confirmado</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                attendeesListContainer.innerHTML += attendeeHtml;
                            });
                        } else {
                             document.write(`
                                <div class="col-12" id="no-attendees-message">
                                    <p class="text-muted fst-italic">Aún no hay participantes confirmados para este evento.</p>
                                </div>
                            `);
                        }
                    </script>
                    <!-- FIN SIMULACIÓN @forelse -->
                </div>
            </div>
        </div>

        <!-- Contenido de Gastos y Liquidación -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <!-- SIMULACIÓN @if ($isAttending || $isOrganizer) -->
                <script>
                    if (SIMULATED_IS_ATTENDING || SIMULATED_IS_ORGANIZER) {
                        document.write(`
                            <!-- Sección de Gastos -->
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
                                    <!-- Aquí iría la lista de gastos reales, por ahora solo el total -->
                                    <div class="pt-3 border-top mt-3 d-flex justify-content-between fw-bold fs-5">
                                        <span>Total Gastos:</span>
                                        <span class="text-danger" id="total-expenses-display">
                                            -${SIMULATED_EXPENSES.toFixed(2)} €
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `);
                    } else {
                        document.write(`
                            <!-- Mensaje si no está asistiendo -->
                            <div class="alert alert-secondary" role="alert">
                                Únete al evento para ver y participar en la gestión de gastos y liquidación.
                            </div>
                        `);
                    }
                </script>
                <!-- FIN SIMULACIÓN @if -->
            </div>

            <div class="col-lg-12">
                <!-- SIMULACIÓN @if ($isAttending || $isOrganizer) -->
                <script>
                    if (SIMULATED_IS_ATTENDING || SIMULATED_IS_ORGANIZER) {
                        document.write(`
                            <!-- Sección de Liquidación -->
                            <div class="card shadow-sm rounded-3">
                                <div class="card-body">
                                    <h2 class="h4 fw-bold text-dark border-bottom pb-2 mb-3">
                                        <i class="bi bi-wallet-fill me-2 custom-text-green"></i> Liquidación y Saldos
                                    </h2>
                                    <!-- Contenido de Liquidación (Placeholder) -->
                                    <div class="alert alert-warning text-center">
                                        Aún no se ha realizado la liquidación de cuentas.
                                    </div>
                                    <button type="button" class="btn btn-custom-green w-100 fw-bold mt-4 shadow-sm rounded-pill">
                                        <i class="bi bi-bank me-1"></i> Liquidar Cuentas Ahora
                                    </button>
                                </div>
                            </div>
                        `);
                    }
                </script>
                <!-- FIN SIMULACIÓN @if -->
            </div>
        </div>
    </div>
    <!-- FIN DEL CONTENIDO DE LA VISTA (@endsection) -->

    <!-- ------------------------------------------------------------------------- -->
    <!-- MODAL PARA AÑADIR GASTO -->
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
                    <form action="#" id="addExpenseForm">
                        <!-- Campo Payer ID (Quién pagó) -->
                        <div class="mb-3">
                            <label for="payer_id" class="form-label fw-bold">Pagado por:</label>
                            <select class="form-select" id="payer_id" name="payer_id" required>
                                <!-- Opciones rellenadas por JS (simulación) -->
                                <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        const select = document.getElementById('payer_id');
                                        if (select) {
                                            window.EventData.attendees.forEach(attendee => {
                                                const option = document.createElement('option');
                                                option.value = attendee.id;
                                                option.textContent = attendee.name + (attendee.id === window.EventData.currentUserId ? ' (Tú)' : '');
                                                select.appendChild(option);
                                                if (attendee.id === window.EventData.currentUserId) {
                                                    option.selected = true; // Por defecto el usuario actual
                                                }
                                            });
                                        }
                                    });
                                </script>
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


    <!-- MODAL PARA EL POPUP DE CONFIRMACIÓN/CANCELACIÓN (Asistencia/Error) -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center border-0 shadow-lg">
                <div class="modal-body p-5">
                    <div id="modal-icon" class="mb-3">
                        <!-- Icono dinámico (rellenado por JS) -->
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

</body>

</html>
