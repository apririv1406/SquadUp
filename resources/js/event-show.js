// public/js/event-show.js
// Lógica de asistencia y gastos. Lee el JSON inyectado en Blade y actúa sobre el DOM.
// Asegúrate de que Bootstrap JS esté cargado antes de este script (defer está bien).

(function () {
    // Parseo seguro del JSON inyectado
    const jsonEl = document.getElementById("event-data-json");
    window.EventData = jsonEl ? JSON.parse(jsonEl.textContent || "{}") : {};

    document.addEventListener("DOMContentLoaded", function () {
        // Estado y referencias
        const eventId = window.EventData.eventId;
        let isAttending = !!window.EventData.isAttending;
        let attendeeCount = Number(window.EventData.attendeeCount || 0);
        const currentUserId = window.EventData.currentUserId;
        const userName = window.EventData.userName;
        const eventTitle = window.EventData.eventTitle || "";
        const attendees = Array.isArray(window.EventData.attendees)
            ? window.EventData.attendees
            : [];
        const csrfToken = window.EventData.csrfToken || "";

        const buttonAttendance = document.getElementById("attendance-button");

        if (buttonAttendance) {
            buttonAttendance.addEventListener("click", toggleAttendance);
        }

        const countDisplay = document.getElementById("attendees-count");
        const countDisplayParticipants = document.getElementById(
            "attendees-count-participants"
        );
        const statusBox = document.getElementById("attendance-status-box");
        const attendeesListContainer = document.getElementById(
            "attendees-list-container"
        );

        const statusModalElement = document.getElementById("statusModal");
        const expenseModalElement = document.getElementById("addExpenseModal");

        const statusModal =
            typeof bootstrap !== "undefined" && statusModalElement
                ? new bootstrap.Modal(statusModalElement, {})
                : null;
        const expenseModal =
            typeof bootstrap !== "undefined" && expenseModalElement
                ? new bootstrap.Modal(expenseModalElement, {})
                : null;

        const modalTitle = document.getElementById("modal-title");
        const modalIcon = document.getElementById("modal-icon");
        const modalBodyText = document.getElementById("modal-body-text");

        const expenseForm = document.getElementById("addExpenseForm");
        const payerSelect = document.getElementById("payer_id");
        const expenseSaveButton = document.getElementById(
            "save-expense-button"
        );
        const expenseFormAlert = document.getElementById("expense-form-alert");

        const deleteModal = document.getElementById(
            "confirmDeleteExpenseModal"
        );
        const deleteForm = document.getElementById("deleteExpenseForm");

        deleteModal.addEventListener("show.bs.modal", function (event) {
            const buttonDelete = event.relatedTarget;
            const action = buttonDelete.getAttribute("data-action");
            deleteForm.setAttribute("action", action);
        });

        // Utilidades
        function setProcessing(btn, text) {
            if (!btn) return;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ${text}`;
        }

        function showStatusModal(title, body, type) {
            if (!statusModal) return;
            if (modalTitle) modalTitle.textContent = title;
            if (modalBodyText) modalBodyText.textContent = body;

            let iconHtml = "";
            let iconColor = "";
            if (type === "join") {
                iconHtml = '<i class="bi bi-check-circle-fill display-3"></i>';
                iconColor = "text-success";
            } else if (type === "leave") {
                iconHtml = '<i class="bi bi-x-circle-fill display-3"></i>';
                iconColor = "text-danger";
            } else {
                iconHtml =
                    '<i class="bi bi-exclamation-triangle-fill display-3"></i>';
                iconColor = "text-warning";
            }
            if (modalIcon)
                modalIcon.innerHTML = `<span class="${iconColor}">${iconHtml}</span>`;
            statusModal.show();
        }

        function updateUI(attending) {
            isAttending = attending;
            if (buttonAttendance) {
                buttonAttendance.classList.remove(
                    "btn-custom-green",
                    "btn-custom-red",
                    "btn-warning"
                );
                buttonAttendance.classList.add(
                    attending ? "btn-custom-red" : "btn-custom-green"
                );
                const desired = attending
                    ? '<i class="bi bi-x-circle me-1"></i> Salir del Evento'
                    : '<i class="bi bi-person-add me-1"></i> Confirmar asistencia';
                if (buttonAttendance.innerHTML !== desired) buttonAttendance.innerHTML = desired;
            }

            if (statusBox) {
                statusBox.classList.toggle("d-none", !attending);
                statusBox.classList.remove("alert-warning");
                statusBox.classList.add("alert-success");
            }

            if (countDisplay) countDisplay.textContent = String(attendeeCount);
            if (countDisplayParticipants)
                countDisplayParticipants.textContent = String(attendeeCount);

            updateAttendeesList(attending);
        }

        function updateAttendeesList(attending) {
            if (!attendeesListContainer) return;
            const noMessage = document.getElementById("no-attendees-message");
            const hasItems =
                attendeesListContainer.querySelectorAll(".attendee-item")
                    .length > 0;
            if (attending && hasItems) {
                if (noMessage) noMessage.classList.add("d-none");
            } else if (!attending && !hasItems) {
                if (noMessage) noMessage.classList.remove("d-none");
            }
        }

        async function toggleAttendance() {
            if (!currentUserId || !buttonAttendance || !csrfToken || !eventId) {
                console.warn("toggleAttendance: datos incompletos", {
                    currentUserId,
                    csrfToken,
                    eventId,
                });
                return;
            }

            try {
                buttonAttendance.disabled = true;
                setProcessing(buttonAttendance, "Procesando...");

                const action = isAttending ? "leave" : "join";
                const url = `/events/${eventId}/${action}`;
                const payload = { event_id: eventId, user_id: currentUserId };

                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (e) {
                    console.warn("No JSON en respuesta", e);
                }

                if (response.ok) {
                    const newAttendingStatus = !!data.attending;

                    // Actualiza UI
                    updateUI(newAttendingStatus);

                    if (newAttendingStatus) {
                        showStatusModal(
                            "¡Asistencia Confirmada!",
                            `Has confirmado correctamente tu asistencia a ${eventTitle}.`,
                            "join"
                        );
                        // Recarga la página para que aparezca el mensaje "Ya has confirmado tu asistencia."
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showStatusModal(
                            "Asistencia Cancelada",
                            `Tu asistencia ha sido cancelada para ${eventTitle}.`,
                            "leave"
                        );
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } else {
                    console.error(
                        "toggleAttendance: respuesta no OK",
                        response.status,
                        data
                    );
                    if (response.status === 419 || response.status === 403) {
                        showStatusModal(
                            "Error de sesión",
                            "Tu sesión ha expirado. Recarga la página e inténtalo de nuevo.",
                            "error"
                        );
                    } else if (response.status === 409) {
                        showStatusModal(
                            "Atención",
                            data.message || "No se pudo actualizar el estado.",
                            "warning"
                        );
                    } else {
                        showStatusModal(
                            "Error",
                            data.message || "Error al procesar la solicitud.",
                            "error"
                        );
                    }
                }
            } catch (err) {
                console.error("toggleAttendance: excepción", err);
                showStatusModal(
                    "Error de Conexión",
                    "No se ha podido conectar con el servidor. Intenta de nuevo.",
                    "error"
                );
            } finally {
                buttonAttendance.disabled = false;
            }
        }

        // Gastos
        function populatePayerSelect() {
            if (!payerSelect) return;
            payerSelect.innerHTML =
                '<option value="" disabled selected>Selecciona un pagador</option>';
            (attendees || []).forEach((attendee) => {
                const option = document.createElement("option");
                option.value = attendee.id;
                option.textContent = attendee.name;
                if (String(attendee.id) === String(currentUserId))
                    option.selected = true;
                payerSelect.appendChild(option);
            });
        }

        async function handleAddExpense(e) {
            e.preventDefault();
            if (!expenseSaveButton || !expenseFormAlert) return;

            expenseSaveButton.disabled = true;
            setProcessing(expenseSaveButton, "Guardando...");
            expenseFormAlert.classList.add("d-none");
            expenseFormAlert.classList.remove("alert-danger", "alert-success");

            const amount = parseFloat(
                document.getElementById("amount")?.value || "0"
            );
            const description = (
                document.getElementById("description")?.value || ""
            ).trim();
            const payerId = document.getElementById("payer_id")?.value || "";

            if (isNaN(amount) || amount <= 0 || !description || !payerId) {
                expenseFormAlert.textContent =
                    "Por favor, asegúrate de rellenar todos los campos correctamente.";
                expenseFormAlert.classList.add("alert-danger");
                expenseFormAlert.classList.remove("d-none");
                expenseSaveButton.disabled = false;
                expenseSaveButton.innerHTML =
                    '<i class="bi bi-plus-circle me-1"></i> Guardar Gasto';
                return;
            }

            const url = `/events/${eventId}/expense`;
            const payload = { amount, description, payer_id: payerId };

            try {
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                });

                const result = await response.json();

                if (response.ok) {
                    alert(result.message || "Gasto registrado con éxito.");
                    window.location.reload();
                } else {
                    console.error(
                        "Error al registrar gasto:",
                        response.status,
                        result
                    );
                    alert(
                        result.message ||
                            "No se pudo registrar el gasto. Intenta de nuevo."
                    );
                }
            } catch (err) {
                console.error("Excepción al registrar gasto:", err);
                alert("Error de conexión con el servidor. Intenta más tarde.");
            } finally {
                expenseSaveButton.disabled = false;
                expenseSaveButton.innerHTML =
                    '<i class="bi bi-plus-circle me-1"></i> Guardar Gasto';
            }
        }

        // función para insertar una fila de gasto en la tabla
        function appendExpenseToList(expense) {
            const tbody = document.getElementById("expenses-list-body");
            if (!tbody) return;

            // eliminar fila "no-expenses-row" si existe
            const noRow = document.getElementById("no-expenses-row");
            if (noRow) noRow.remove();

            const tr = document.createElement("tr");
            tr.setAttribute("data-expense-id", expense.id);

            tr.innerHTML = `
    <td class="ps-3 fw-semibold">${escapeHtml(expense.payer_name)}</td>
    <td>${escapeHtml(expense.description)}</td>
    <td class="text-end text-danger">-${escapeHtml(expense.amount)} €</td>
    <td class="text-muted small">${escapeHtml(expense.created_at)}</td>
    `;

            // insertar al principio
            tbody.prepend(tr);

            // actualizar total
            const totalEl = document.getElementById("total-expenses");
            if (totalEl && typeof expense.total !== "undefined") {
                totalEl.textContent = `-${expense.total} €`;
            }
        }

        // helper simple para escapar texto (evita XSS)
        function escapeHtml(str) {
            if (str === null || typeof str === "undefined") return "";
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });
})();
