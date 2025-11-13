<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal del usuario con sus próximos eventos (RF10)
     * y el balance global de liquidación (RF6).
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Cargar los eventos próximos (RF10)
        // Se utiliza la relación 'eventsAttending' definida en el modelo User
        $upcomingEvents = $user->eventsAttending()
            ->where('event_date', '>=', Carbon::now())
            ->orderBy('event_date', 'asc')
            ->get();

        // 2. Calcular el balance global (RF6)
        $globalBalance = $this->calculateGlobalBalance($user);

        // 3. Pasar ambas variables a la vista
        return view('dashboard', [
            'upcomingEvents' => $upcomingEvents,
            'globalBalance'  => $globalBalance, // <-- Esto soluciona el error
        ]);
    }

    /**
     * Calcula el balance neto global del usuario (RF6).
     * Itera sobre todos los eventos a los que el usuario ha asistido
     * y suma sus deudas y créditos.
     */
    private function calculateGlobalBalance(User $user)
    {
        $totalCredit = 0;
        $totalDebt = 0;

        // Obtener todos los eventos donde el usuario está confirmado
        // Eager load (cargar) las relaciones necesarias para el cálculo
        $attendedEvents = $user->eventsAttending()
                            ->with([
                                'attendance' => function ($query) {
                                    $query->where('is_confirmed', true);
                                },
                                'expenses.payer'
                            ])
                            ->get();

        foreach ($attendedEvents as $event) {

            // Contar asistentes confirmados para *este* evento
            $attendeesCount = $event->attendance->count();

            // Si no hay asistentes confirmados, no se puede dividir el gasto
            if ($attendeesCount === 0) {
                continue;
            }

            // Calcular la parte de cada gasto
            foreach ($event->expenses as $expense) {
                $share = $expense->amount / $attendeesCount;

                // A. El usuario pagó (CRÉDITO)
                if ($expense->payer_id === $user->user_id) {
                    // El crédito es la parte de los *demás* asistentes
                    $othersCount = $attendeesCount - 1;
                    if ($othersCount > 0) {
                         $totalCredit += $share * $othersCount;
                    }
                }

                // B. El usuario NO pagó (DEUDA)
                // (Ya sabemos que el usuario está confirmado por la consulta $attendedEvents)
                if ($expense->payer_id !== $user->user_id) {
                    // La deuda es la parte del usuario
                    $totalDebt += $share;
                }
            }
        }

        return [
            'credit' => round($totalCredit, 2),
            'debt'   => round($totalDebt, 2),
            'net'    => round($totalCredit - $totalDebt, 2),
        ];
    }
}

