<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Gestiona la creación, edición, eliminación y asistencia a Eventos (RF1, RF3, RF4, RF6, RF13).
 */
class EventController extends Controller
{
    // Define los roles que pueden crear/editar eventos
    protected const EVENT_MANAGEMENT_ROLES = [
        Group::ROLE_ADMIN,
        Group::ROLE_ORGANIZER
    ];

    /**
     * Muestra la lista de eventos públicos para exploración.
     * * @param \Illuminate\Http\Request $request
     */
    public function explore(Request $request)
    {
        // 1. Lista fija de deportes
        $availableSports = [
            'futbol' => 'Fútbol',
            'futbol_sala' => 'Fútbol sala',
            'baloncesto' => 'Baloncesto',
            'balonmano' => 'Balonmano',
            'waterpolo' => 'Waterpolo',
            'tenis' => 'Tenis',
            'voley' => 'Voleibol',
            'running' => 'Running / Carrera',
            'senderismo' => 'Senderismo',
            'padel' => 'Pádel'
        ];

        // 2. Filtros del usuario
        $currentSport = $request->input('sport');
        if (!in_array($currentSport, $availableSports)) {
            $currentSport = null;
        }

        $currentLocation = trim($request->input('location'));
        $currentLocation = empty($currentLocation) ? null : $currentLocation;

        $minCapacity = filter_var($request->input('min_capacity'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $minCapacity = $minCapacity === false ? null : $minCapacity;

        $maxCapacity = filter_var($request->input('max_capacity'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $maxCapacity = $maxCapacity === false ? null : $maxCapacity;

        $minDate = $request->input('min_date') ?: null;
        $maxDate = $request->input('max_date') ?: null;

        // Usuario autenticado
        $user = Auth::user();

        /**
         * 3. CONSULTA BASE CORREGIDA
         *
         * Antes SOLO mostrabas eventos cuyo GRUPO era público.
         * Ahora mostramos:
         *   ✔ eventos públicos (event.is_public = 1)
         *   ✔ eventos de grupos donde el usuario es miembro
         *   ✔ eventos creados por el usuario
         *
         * Manteniendo el mismo orden y la misma estructura.
         */
        $publicEventsQuery = Event::query()
            ->with(['group', 'attendees'])
            ->withCount([
                'attendees as confirmed_attendees_count' => function ($q) {
                    $q->where('is_confirmed', true);
                }
            ])
            ->where('is_public', 1) // eventos públicos
            ->orWhereHas('group.members', function ($q) use ($user) {
                $q->where('users.user_id', $user->user_id); // eventos de mis grupos
            })
            ->orWhere('creator_id', $user->user_id) // eventos creados por mí
            ->orderBy('event_date', 'desc'); // MISMO ORDEN QUE TENÍAS


        // 4. Aplicar filtros condicionales

        if ($currentSport) {
            $publicEventsQuery->where('sport_name', $currentSport);
        }

        if ($currentLocation) {
            $publicEventsQuery->where('location', 'like', '%' . $currentLocation . '%');
        }

        if ($minCapacity !== null && $maxCapacity !== null) {
            $publicEventsQuery->whereBetween('capacity', [$minCapacity, $maxCapacity]);
        } elseif ($minCapacity !== null) {
            $publicEventsQuery->where('capacity', '>=', $minCapacity);
        } elseif ($maxCapacity !== null) {
            $publicEventsQuery->where('capacity', '<=', $maxCapacity);
        }

        if ($minDate && $maxDate) {
            $publicEventsQuery->whereBetween('event_date', [$minDate, $maxDate]);
        } elseif ($minDate) {
            $publicEventsQuery->where('event_date', '>=', $minDate);
        } elseif ($maxDate) {
            $publicEventsQuery->where('event_date', '<=', $maxDate);
        }

        // 5. Paginación (igual que antes)
        $events = $publicEventsQuery->paginate(12)->withQueryString();

        // 6. Enviar datos a la vista
        return view('events.explore', [
            'events' => $events,
            'user' => $user,
            'availableSports' => $availableSports,
            'currentSport' => $currentSport,
            'currentLocation' => $currentLocation,
            'minCapacity' => $minCapacity,
            'maxCapacity' => $maxCapacity,
            'minDate' => $minDate,
            'maxDate' => $maxDate,
        ]);
    }


    public function create(Request $request)
    {
        $groupId = $request->group_id; // puede venir o no

        // Si viene un group_id, buscamos el grupo
        $group = null;
        if ($groupId) {
            $group = \App\Models\Group::find($groupId);
        }

        // Lista de deportes
        $availableSports = [
            'futbol' => 'Fútbol',
            'futbol_sala' => 'Fútbol sala',
            'baloncesto' => 'Baloncesto',
            'balonmano' => 'Balonmano',
            'waterpolo' => 'Waterpolo',
            'tenis' => 'Tenis',
            'voley' => 'Voleibol',
            'running' => 'Running / Carrera',
            'senderismo' => 'Senderismo',
            'padel' => 'Pádel'
        ];

        return view('events.create', compact('group', 'groupId', 'availableSports'));
    }


    public function store(Request $request)
    {
        $availableSports = [
            'futbol',
            'futbol_sala',
            'baloncesto',
            'balonmano',
            'waterpolo',
            'tenis',
            'voley',
            'running',
            'senderismo',
            'padel'
        ];

        $validated = $request->validate([
            'group_id'   => 'required|exists:groups,group_id',
            'title'      => 'required|string|max:255',
            'sport'      => ['required', Rule::in($availableSports)],
            'location'   => 'required|string|max:255',
            'event_date' => 'required|date|after:now',
            'capacity'   => 'nullable|integer|min:1',
            'is_public'  => 'boolean',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isAuthorized = $user->hasRoleInGroup(
            $validated['group_id'],
            [User::ROLE_ADMIN, User::ROLE_ORGANIZER]
        );

        // Crear el evento
        $event = Event::create([
            'group_id'   => $validated['group_id'],
            'title'      => $validated['title'],
            'location'   => $validated['location'],
            'event_date' => Carbon::parse($validated['event_date']),
            'sport_name' => $validated['sport'],
            'is_public'  => $validated['is_public'] ?? false,
            'capacity'   => $validated['capacity'] ?? 0,
            'creator_id' => $user->user_id,
        ]);

        // Registrar asistencia automática del creador
        $event->attendees()->attach($user->user_id, ['is_confirmed' => true]);

        return redirect()->route('event.show', $event->event_id)
            ->with('success', 'Evento creado correctamente.');
    }


    /**
     * Muestra la vista detallada de un evento específico.
     * @param \App\Models\Event $event La instancia del evento inyectada por Route Model Binding.
     * @return \Illuminate\View\View
     */
    public function show(Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cargar las relaciones necesarias
        $event->load(['group', 'attendees', 'expenses.payer', 'creator']);

        // 1. Obtener asistentes CONFIRMADOS (is_confirmed = true)
        $confirmedAttendees = $event->attendees()
            ->wherePivot('is_confirmed', true)
            ->get();

        // 2. Obtener gastos del evento (modelos Eloquent con payer)
        $eventExpenses = $event->expenses()->with('payer')->orderBy('created_at', 'desc')->get();
        $totalExpenses = (float) $eventExpenses->sum('amount');

        // 3. Verificar si el usuario es miembro del grupo
        $isGroupMember = $user->groups->contains($event->group_id);

        if (! $isGroupMember && ! $event->is_public) {
            return redirect()->route('groups.index')->with('error', 'Acceso denegado. El evento no es público y no eres miembro del grupo.');
        }

        // 4. Determinar el estado de asistencia del usuario (usar confirmedAttendees para consistencia)
        $isAttending = $confirmedAttendees->contains('user_id', $user->user_id);

        // 5. Determinar el permiso de organizador/administrador para la vista
        $isOrganizer = $user->hasRoleInGroup($event->group_id, self::EVENT_MANAGEMENT_ROLES);

        // 6. Preparar balances y liquidaciones
        // Número de participantes que comparten los gastos (los confirmados)
        $participantCount = max(1, $confirmedAttendees->count());

        // 6.1. Cuánto ha pagado cada usuario (sumando por payer_id)
        $paidPerUser = $eventExpenses
            ->groupBy('payer_id')
            ->map(fn($group) => (float) $group->sum('amount'));

        // Asegurar que todos los confirmados aparecen en el mapa (aunque no hayan pagado)
        $paidPerUser = $confirmedAttendees->pluck('user_id')
            ->mapWithKeys(fn($id) => [$id => $paidPerUser->get($id, 0.0)]);

        // 6.2. Parte que le corresponde a cada uno
        $sharePerPerson = $participantCount > 0 ? round($totalExpenses / $participantCount, 2) : 0.0;

        // 6.3. Construir balances: positivo = acreedor (le deben), negativo = deudor (debe)
        $balances = $confirmedAttendees->map(function ($u) use ($paidPerUser, $sharePerPerson) {
            $paid = (float) ($paidPerUser->get($u->user_id, 0.0) ?? 0.0);
            $balance = round($paid - $sharePerPerson, 2);
            return (object) [
                'user_id' => $u->user_id,
                'name' => $u->name,
                'paid' => $paid,
                'share' => round($sharePerPerson, 2),
                'balance' => $balance,
            ];
        })->values();

        // 6.4. Generar lista de liquidación (pares deudor -> acreedor : cantidad)
        $settlements = $this->computeSettlements($balances);

        // 7. Pasar las variables a la vista
        return view('events.show', [
            'event' => $event,
            'confirmedAttendees' => $confirmedAttendees,
            'expenses' => $totalExpenses, // compatibilidad con variable previa
            'isAttending' => $isAttending,
            'isOrganizer' => $isOrganizer,
            'eventExpenses' => $eventExpenses,
            'totalExpenses' => $totalExpenses,
            'balances' => $balances,
            'settlements' => $settlements,
        ]);
    }
    /**
     * Actualiza la asistencia del usuario a un evento (RF4).
     *
     * @param  \App\Models\Event  $event
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleAttendance(Event $event, Request $request)
    {
        $user = Auth::user();

        $isAttending = $event->attendees()->wherePivot('is_confirmed', true)->where('user_id', $user->user_id)->exists();

        if ($isAttending) {
            $event->attendees()->detach($user->user_id);
            $message = 'Has cancelado tu asistencia al evento.';
        } else {
            $attendeesCount = $event->attendees()->wherePivot('is_confirmed', true)->count();
            if ($event->capacity > 0 && $attendeesCount >= $event->capacity) {
                return back()->with('error', 'Lo sentimos, el evento ha alcanzado su capacidad máxima.');
            }

            $event->attendees()->attach($user->user_id, [
                'is_confirmed' => true,
                'confirmation_date' => now(),
            ]);
            $message = '¡Asistencia confirmada!';
        }

        return back()->with('success', $message);
    }

    /**
     * Muestra el formulario para editar un evento (RF1).
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // --- VERIFICACIÓN DE PERMISOS CLAVE ---
        $isAuthorized = $user->hasRoleInGroup($event->group_id, self::EVENT_MANAGEMENT_ROLES);

        if (!$isAuthorized) {
            return back()->with('error', 'No tienes permiso para editar este evento.');
        }

        // Lista de deportes disponibles para el desplegable
        $sportsList = [
            'futbol' => 'Fútbol',
            'futbol_sala' => 'Fútbol sala',
            'baloncesto' => 'Baloncesto',
            'balonmano' => 'Balonmano',
            'waterpolo' => 'Waterpolo',
            'tenis' => 'Tenis',
            'voley' => 'Voleibol',
            'running' => 'Running / Carrera',
            'senderismo' => 'Senderismo',
            'padel' => 'Pádel'
        ];

        // Se pasa el evento y la lista de deportes a la vista
        return view('events.edit', compact('event', 'sportsList'));
    }

    /**
     * Actualiza un evento en la base de datos (RF1).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // --- VERIFICACIÓN DE PERMISOS CLAVE ---
        $isAuthorized = $user->hasRoleInGroup($event->group_id, self::EVENT_MANAGEMENT_ROLES);

        if (!$isAuthorized) {
            return back()->with('error', 'No tienes permiso para actualizar este evento.');
        }

        // Reglas de validación
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'after_or_equal:now'],
            'sport_name' => ['required', 'string', 'max:100'],
            'is_public' => ['nullable', 'boolean'],
            'capacity' => ['nullable', 'integer', 'min:0'],
        ]);

        // Actualización del evento
        $event->update([
            'title' => $validated['title'],
            'location' => $validated['location'],
            'event_date' => Carbon::parse($validated['event_date']),
            'sport_name' => $validated['sport_name'],
            'is_public' => $request->has('is_public'), // Asegura que se almacene 'false' si no se marca
            'capacity' => $validated['capacity'] ?? 0,
        ]);

        return redirect()->route('event.show', $event)->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Elimina un evento de la base de datos (RF1).
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // --- VERIFICACIÓN DE PERMISOS CLAVE ---
        $isAuthorized = $user->hasRoleInGroup($event->group_id, self::EVENT_MANAGEMENT_ROLES);

        if (!$isAuthorized) {
            return back()->with('error', 'No tienes permiso para eliminar este evento.');
        }

        $eventTitle = $event->title;
        $groupId = $event->group_id; // Obtenemos el ID del grupo para redirigir
        $event->delete();

        return redirect()->route('groups.show', $groupId)->with('success', 'Evento "' . $eventTitle . '" eliminado correctamente.');
    }

    // =======================================================
    // MÉTODOS DE ASISTENCIA (JOIN/LEAVE)
    // =======================================================

    /**
     * El usuario autenticado se une al evento (crea un registro de asistencia).
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function join(Request $request, Event $event)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $id = $user->user_id ?? $user->id;

        if (! $event->attendees()->where('attendance.user_id', $id)->exists()) {
            $event->attendees()->attach($id, [
                'is_confirmed' => 1,
                'confirmation_date' => now(),
            ]);
        }

        return response()->json([
            'attending' => true,
            'attendeeCount' => $event->attendees()->count(),
        ]);
    }

    /**
     * El usuario autenticado abandona el evento (elimina el registro de asistencia).
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function leave(Request $request, \App\Models\Event $event)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $userId = $user->user_id ?? $user->id;
        $userId = (int) $userId;

        // LOG para depuración: tipos y valores antes de detach
        Log::debug('Leave: detaching user from event', [
            'event_id' => $event->event_id ?? $event->id,
            'user_id' => $userId,
            'type_user_id' => gettype($userId),
        ]);

        // detach acepta int o array de ints
        $event->attendees()->detach($userId);

        return response()->json(['attending' => false, 'user_id' => $userId]);
    }

    // =======================================================
    // MÉTODOS DE LIQUIDACION
    // =======================================================


    /**
     * Genera pares de liquidación: [{from_id, from_name, to_id, to_name, amount}, ...]
     * Recibe balances: collection de objetos con user_id, name, balance (float)
     */
    protected function computeSettlements($balances)
    {
        // separar acreedores y deudores
        $creditors = $balances->filter(fn($b) => $b->balance > 0)
            ->map(fn($b) => ['id' => $b->user_id, 'name' => $b->name, 'amount' => $b->balance])
            ->values()
            ->toArray();

        $debtors = $balances->filter(fn($b) => $b->balance < 0)
            ->map(fn($b) => ['id' => $b->user_id, 'name' => $b->name, 'amount' => abs($b->balance)])
            ->values()
            ->toArray();

        $i = 0;
        $j = 0;
        $settlements = [];

        while ($i < count($debtors) && $j < count($creditors)) {
            $debt = $debtors[$i];
            $cred = $creditors[$j];

            $amount = min($debt['amount'], $cred['amount']);
            $amount = round($amount, 2);

            if ($amount > 0) {
                $settlements[] = [
                    'from_id' => $debt['id'],
                    'from_name' => $debt['name'],
                    'to_id' => $cred['id'],
                    'to_name' => $cred['name'],
                    'amount' => $amount,
                ];

                // restar cantidades
                $debtors[$i]['amount'] = round($debtors[$i]['amount'] - $amount, 2);
                $creditors[$j]['amount'] = round($creditors[$j]['amount'] - $amount, 2);
            }

            if ($debtors[$i]['amount'] <= 0.001) $i++;
            if ($creditors[$j]['amount'] <= 0.001) $j++;
        }

        return $settlements;
    }

    public function settle(Request $request, $eventId, $expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        $user = $request->user();

        // Comprobar que el gasto pertenece al evento
        if ($expense->event_id != $eventId) {
            return redirect()->route('event.show', $eventId)
                ->with('error', 'El gasto no pertenece a este evento.');
        }

        // Solo el creador del gasto puede liquidarlo
        if ($expense->payer_id != $user->user_id) {
            return redirect()->route('event.show', $eventId)
                ->with('error', 'Solo el creador puede liquidar este gasto.');
        }

        // Marcar como liquidado
        $expense->settled = true;
        $expense->save();

        return redirect()->route('event.show', $eventId)
            ->with('success', 'El gasto ha sido liquidado.');
    }
}
