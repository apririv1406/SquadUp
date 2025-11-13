document.addEventListener('DOMContentLoaded', function() {
    // 1. Variables de Estado y Elementos del DOM (Obtenidos de Blade/window.EventData)
    const {
        eventId,
        isAttending: initialIsAttending,
        attendeeCount: initialAttendeeCount,
        currentUserId,
        eventTitle,
        csrfToken,
        joinUrl,
        leaveUrl
    } = window.EventData;

    let isAttending = initialIsAttending;
    let attendeeCount = initialAttendeeCount;

    // Elementos de la vista principal (Asistencia)
    const button = document.getElementById('attendance-button');
    const countDisplay = document.getElementById('attendees-count');
    const countDisplayParticipants = document.getElementById('attendees-count-participants');
    const statusBox = document.getElementById('attendance-status-box');
    const attendeesListContainer = document.getElementById('attendees-list-container');

    // Modal de Asistencia/Error (StatusModal)
    const statusModalElement = document.getElementById('statusModal');
    // Asegúrate de que bootstrap está disponible. En un entorno Laravel, lo estará.
    const statusModal = new bootstrap.Modal(statusModalElement, {});
    const modalTitle = document.getElementById('modal-title');
    const modalIcon = document.getElementById('modal-icon');
    const modalBodyText = document.getElementById('modal-body-text');

    // =========================================================================
    // FUNCIONES DE ASISTENCIA (Attendance Logic)
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

    /** Crea el HTML para un nuevo asistente */
    function createAttendeeElement(id, name) {
        const initial = name.charAt(0).toUpperCase();
        return `
            <div class="col attendee-item" data-user-id="${id}">
                <div class="p-2 bg-light rounded-3 d-flex align-items-center">
                    <div class="me-3 d-flex align-items-center justify-content-center btn-custom-green rounded-circle text-white" style="width: 36px; height: 36px; font-size: 0.9rem;">
                        ${initial}
                    </div>
                    <div>
                        <p class="mb-0 fw-bold text-truncate">${name}</p>
                        <p class="mb-0 small text-success">Confirmado</p>
                    </div>
                </div>
            </div>
        `;
    }

    /** Actualiza la lista de asistentes (añade/elimina el elemento del usuario actual) */
    function updateAttendeesList(attending) {
        if (!currentUserId || !window.EventData.userName) return;

        const userName = window.EventData.userName;
        const noMessage = document.getElementById('no-attendees-message');

        let userElement = attendeesListContainer.querySelector(`.attendee-item[data-user-id="${currentUserId}"]`);

        if (attending) {
            // Unirse: Añadir elemento si no existe
            if (!userElement) {
                const newElement = createAttendeeElement(currentUserId, userName);
                attendeesListContainer.insertAdjacentHTML('beforeend', newElement);
                userElement = attendeesListContainer.querySelector(`.attendee-item[data-user-id="${currentUserId}"]`);
            }
            if (noMessage) { noMessage.classList.add('d-none'); }
        } else {
            // Salir: Eliminar elemento si existe
            if (userElement) {
                userElement.remove();
            }
            // Si no quedan asistentes, mostrar el mensaje
            if (attendeesListContainer.querySelectorAll('.attendee-item').length === 0) {
                if (noMessage) { noMessage.classList.remove('d-none'); }
            }
        }
    }


    /** Actualiza el estado del botón y el mensaje superior */
    function updateUI(attending) {
        isAttending = attending;

        // 1. Limpiar clases de estilo previas y actualizar estado del botón
        if (!button) return;

        button.classList.remove('btn-custom-green', 'btn-custom-red', 'btn-warning');
        button.innerHTML = '';

        if (attending) {
            // Estado: Asistiendo (Botón rojo de Cancelar/Salir)
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

        // 3. Actualizar la lista de participantes
        updateAttendeesList(attending);
    }

    // Función Principal de Lógica AJAX para asistencia
    async function toggleAttendance() {
        if (!currentUserId) return;

        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';

        const action = isAttending ? 'leave' : 'join';
        const url = isAttending ? leaveUrl : joinUrl;

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
                    showStatusModal('¡Asistencia Confirmada!', `Has confirmado correctamente tu asistencia a ${eventTitle}. Pulsa "Entendido" para recargar la página y actualizar todos los datos (gastos, liquidación).`, 'join');
                } else if (action === 'leave') {
                    showStatusModal('Asistencia Cancelada', `Tu asistencia ha sido cancelada para ${eventTitle}. Pulsa "Entendido" para recargar la página y actualizar el acceso a la sección de gastos.`, 'leave');
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
            // Si hubo un fallo que no fue manejado por showStatusModal, restaurar el botón.
            if (!success) {
               updateUI(isAttending);
            }
        }
    }

    // =========================================================================
    // INICIALIZACIÓN Y LISTENERS
    // =========================================================================

    // 1. Inicialización de la UI de asistencia
    if (button) {
        updateUI(isAttending);
        button.addEventListener('click', toggleAttendance);
    }

    // 2. Listener para recargar la página al cerrar el StatusModal (asistencia/error)
    statusModalElement.addEventListener('hidden.bs.modal', function () {
        // Recargamos SIEMPRE para asegurar que el Blade (sección de gastos) se actualice.
        window.location.reload();
    });
});
