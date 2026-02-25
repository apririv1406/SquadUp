<?php

namespace App\Http\Controllers;

use App\Models\Event;

use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::orderBy('event_date', 'desc')->get();
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Lista de deportes
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

        // Grupos reales desde la BD
        $groups = \App\Models\Group::all();

        return view('admin.events.edit', compact('event', 'sportsList', 'groups'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate(['title' => 'required|string|max:255', 'event_date' => 'required|date', 'location' => 'nullable|string|max:255', 'description' => 'nullable|string',]);
        $event->update($validated);
        return redirect()->route('admin.events.edit', $event->event_id)->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Buscar gastos asociados al evento
        $expenses = \App\Models\Expense::where('event_id', $event->event_id)->get();

        if ($expenses->isNotEmpty()) {
            // Eliminar todos los gastos asociados (liquidados o no)
            foreach ($expenses as $expense) {
                $expense->delete();
            }
        }

        // Ahora sí se puede eliminar el evento
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Evento y sus gastos asociados eliminados correctamente.');
    }
}
