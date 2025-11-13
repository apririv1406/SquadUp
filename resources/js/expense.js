document.addEventListener('DOMContentLoaded', function() {
    // 1. Variables de Estado y Elementos del DOM (Obtenidos de Blade/window.EventData)
    const {
        attendees,
        csrfToken,
        expenseUrl
    } = window.EventData;

    // Elementos del Modal de Gasto (ExpenseModal)
    const expenseModalElement = document.getElementById('addExpenseModal');
    const expenseForm = document.getElementById('addExpenseForm');
    const payerSelect = document.getElementById('payer_id');
    const expenseSaveButton = document.getElementById('save-expense-button');
    const expenseFormAlert = document.getElementById('expense-form-alert');
    const totalExpensesDisplay = document.getElementById('total-expenses-display');


    // =========================================================================
    // FUNCIONES DE GASTOS (Expense Logic)
    // =========================================================================

    /** Rellena el select de "Pagado por" con los asistentes. */
    function populatePayerSelect() {
        // Limpiar opciones previas
        payerSelect.innerHTML = '<option value="" disabled selected>Selecciona un pagador</option>';

        const currentUserId = window.EventData.currentUserId;

        // Añadir asistentes
        attendees.forEach(attendee => {
            const option = document.createElement('option');
            option.value = attendee.id;
            // Si el pagador es el usuario actual, mostrar "(Tú)" y seleccionarlo.
            option.textContent = attendee.name + (attendee.id === currentUserId ? ' (Tú)' : '');

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

        const amountInput = document.getElementById('amount');
        const descriptionInput = document.getElementById('description');

        const amount = parseFloat(amountInput.value);
        const description = descriptionInput.value.trim();
        const payerId = payerSelect.value;

        // Validaciones
        if (isNaN(amount) || amount <= 0 || !description || !payerId) {
            expenseFormAlert.textContent = 'Por favor, asegúrate de rellenar todos los campos correctamente (Cantidad > 0 y pagador seleccionado).';
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

        try {
            const response = await fetch(expenseUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, // Añadir el token
                    'Accept': 'application/json' // Pedir una respuesta JSON
                },
                body: JSON.stringify(datosGasto)
            });

            const result = await response.json();

            if (response.ok) {
                console.log('Gasto registrado con éxito:', result);

                // Cierre y actualización de la vista. Recargar la página es la forma más segura
                // para que Blade y el total de gastos ($expenses) se actualicen.
                const modal = bootstrap.Modal.getInstance(expenseModalElement);
                if (modal) {
                    modal.hide();
                }

                // Aunque la recarga no estaba explícitamente en el original, es necesaria
                // para que el Blade se actualice con el nuevo total de gastos.
                window.location.reload();


            } else {
                console.error('Error del servidor al guardar el gasto:', result);
                let errorMessage = 'Error desconocido al registrar el gasto.';

                // Manejo de errores de validación de Laravel (código 422)
                if (response.status === 422 && result.errors) {
                    errorMessage = Object.values(result.errors).flat().join(' ');
                } else if (result.message) {
                    errorMessage = result.message;
                }

                expenseFormAlert.textContent = `Error: ${errorMessage}`;
                expenseFormAlert.classList.remove('d-none');
            }
        } catch (error) {
            console.error('Error de red o procesamiento:', error);
            expenseFormAlert.textContent = 'Error de conexión. Por favor, revisa tu red.';
            expenseFormAlert.classList.remove('d-none');
        } finally {
            // Asegurarse de restaurar el botón
            expenseSaveButton.disabled = false;
            expenseSaveButton.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Guardar Gasto';
        }
    }

    // =========================================================================
    // INICIALIZACIÓN Y LISTENERS
    // =========================================================================

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
