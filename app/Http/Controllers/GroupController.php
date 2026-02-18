<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Group;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|max:500',
        ]);

        // Generar código único
        do {
            $code = strtoupper(Str::random(8));
        } while (Group::where('invitation_code', $code)->exists());

        $user = Auth::user();

        // Crear grupo
        $group = Group::create([
            'organizer_id' => $user->user_id,
            'name' => $request->name,
            'description' => $request->description,
            'invitation_code' => $code,
        ]);

        // Añadir al creador como miembro
        UserGroup::create([
            'group_id' => $group->group_id,
            'user_id' => $user->user_id,
        ]);

        return redirect()->route('groups.show', $group->group_id)
            ->with('success', 'Grupo creado correctamente.');
    }

    public function show($group_id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $group = Group::with(['events', 'members', 'organizer'])
            ->findOrFail($group_id);

        // Saber si el usuario autenticado es el organizador
        $isOrganizer = $user->user_id === $group->organizer_id;

        return view('groups.show', compact('group', 'isOrganizer'));
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $groups = $user->groups; // grupos donde el usuario es miembro
        return view('groups.index', compact('groups'));
    }

    public function joinByCode(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'invitation_code' => 'required|string'
        ]);

        // Buscar grupo por código
        $group = Group::where('invitation_code', $request->invitation_code)->first();

        if (!$group) {
            return back()->with('error', 'El código de invitación no es válido.');
        }

        // Comprobar si ya pertenece
        if ($group->members()->where('users.user_id', $user->user_id)->exists()) {
            return back()->with('info', 'Ya eres miembro de este grupo.');
        }

        // Añadir al usuario al grupo
        $group->members()->attach($user->user_id);

        return redirect()->route('groups.show', $group->group_id)
            ->with('success', 'Te has unido al grupo correctamente.');
    }

    public function leave($group_id)
    {
        $user = Auth::user();
        $group = Group::with('events')->findOrFail($group_id);

        // Eliminar participación del usuario en todos los eventos del grupo
        foreach ($group->events as $event) {
            $event->attendees()->detach($user->user_id);
        }

        // Eliminar al usuario del grupo
        $group->members()->detach($user->user_id);

        return redirect()->route('groups.index')
            ->with('success', 'Has abandonado el grupo y se ha eliminado tu participación en sus eventos.');
    }
}
