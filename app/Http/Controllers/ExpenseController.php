<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Event;

class ExpenseController extends Controller
{
    /**
     * Guarda un nuevo gasto asociado a un evento.
     * @param  \Illuminate\Http\Request  $request
     * @param  int $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $eventId)
    {
        // 1. Validación de los datos
        try {
            $validatedData = $request->validate([
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01',
                'payer_id' => 'required|exists:users,user_id',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        }

        // --- PUNTO CRÍTICO DE DEBUGGING ---
        try {
            // 2. Creación del gasto
            $expense = Expense::create([
                'event_id' => $eventId,
                'description' => $validatedData['description'],
                'amount' => $validatedData['amount'],
                'payer_id' => $validatedData['payer_id'],
            ]);

            // 3. Respuesta correcta para AJAX (JSON)
            return response()->json([
                'message' => 'Gasto registrado con éxito.',
                'expense' => $expense
            ], 201); // 201 Created

        } catch (\Exception $e) {
            // Si hay un error, detenemos la ejecución y mostramos el mensaje exacto
            // que nos dice si es Mass Assignment o un error de columna/null.
            // Una vez que tengas el error, ELIMINA este 'dd()'
            dd('Error FATAL en la base de datos o modelo: ' . $e->getMessage());

            // Si no quieres usar dd(), puedes devolver el mensaje de error en el JSON
            /* return response()->json([
                'message' => 'Error interno del servidor al guardar el gasto.',
                'error_details' => $e->getMessage()
            ], 500);
            */
        }
    }
}
