<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Event;

class ExpenseController extends Controller
{
    public function store(Request $request, $eventId)
    {
        // 1. Validación de los datos
        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'payer_id'    => 'required|exists:users,user_id',
        ]);

        // 2. Comprobar que el usuario está autorizado
        $event = Event::findOrFail($eventId);
        $user  = $request->user();

        $isOrganizer = ($event->organizer_id == $user->id);
        $isAttending = $event->attendees()
            ->wherePivot('is_confirmed', true)
            ->wherePivot('user_id', $user->id)
            ->exists();

        if (! $isOrganizer && ! $isAttending) {
            return redirect()
                ->route('event.show', $eventId)
                ->with('error', 'No puedes registrar gastos porque no estás asistiendo a este evento.');
        }

        // 3. Intentar crear el gasto
        try {
            Expense::create([
                'event_id'    => $eventId,
                'description' => $validatedData['description'],
                'amount'      => $validatedData['amount'],
                'payer_id'    => $validatedData['payer_id'],
            ]);

            return redirect()
                ->route('event.show', $eventId)
                ->with('success', 'Gasto registrado con éxito.');
        } catch (\Exception $e) {
            // 4. Si ocurre un error inesperado, capturarlo y mostrar mensaje
            return redirect()
                ->route('event.show', $eventId)
                ->with('error', 'Error interno al registrar el gasto: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $eventId, $expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        $user    = $request->user();

        // Comprobar que el gasto pertenece al evento
        if ($expense->event_id != $eventId) {
            return redirect()
                ->route('event.show', $eventId)
                ->with('error', 'El gasto no pertenece a este evento.');
        }

        // Comprobar que el usuario es el creador del gasto o el organizador
        $event = Event::findOrFail($eventId);
        $isOrganizer = ($event->organizer_id == $user->id);

        if ($expense->payer_id != $user->id && ! $isOrganizer) {
            return redirect()
                ->route('event.show', $eventId)
                ->with('error', 'No tienes permiso para eliminar este gasto.');
        }

        // Eliminar el gasto
        $expense->delete();

        return redirect()
            ->route('event.show', $eventId)
            ->with('success', 'Gasto eliminado correctamente.');
    }
}
