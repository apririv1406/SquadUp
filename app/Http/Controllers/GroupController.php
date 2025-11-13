<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group; // Asegúrate de que este modelo exista y esté correctamente configurado

class GroupController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo grupo.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Almacena un nuevo grupo en la base de datos.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validación de la solicitud
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            // El código es opcional (nullable), y si se proporciona, debe ser único y tener 10 caracteres
            'invitation_code' => 'nullable|string|max:10|unique:groups,invitation_code',
        ]);

        // 2. Definición del organizador y del código de invitación

        $organizerId = Auth::id();

        if (!$organizerId) {
            // Esto debería ser atrapado por el middleware 'auth', pero es una buena práctica de seguridad
            return back()->withErrors(['auth' => 'Debes iniciar sesión para crear un grupo.'])->withInput();
        }

        // Si el usuario no proporcionó un código, generamos uno automáticamente
        $invitationCode = $validatedData['invitation_code'] ?? null;

        if (empty($invitationCode)) {
            // Generación simple y única de un código de 8 caracteres
            do {
                $invitationCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            } while (Group::where('invitation_code', $invitationCode)->exists()); // Asegura unicidad
        }


        // 3. Creación del grupo en la base de datos
        try {
            $group = Group::create([
                'organizer_id' => $organizerId,
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'invitation_code' => $invitationCode,
            ]);

            // 4. Redirección al grupo recién creado
            return redirect()->route('groups.show', $group->group_id)
                             ->with('success', '¡Grupo "' . $group->name . '" creado con éxito! Código: ' . $group->invitation_code);

        } catch (\Exception $e) {
            // Si hay un error de base de datos (ej. problema de conexión o FK faltante)
            // En un entorno real, solo registraríamos el error, pero aquí lo mostramos para depuración.
            return back()->withErrors(['database' => 'Error al guardar el grupo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Muestra los detalles de un grupo específico.
     * @param string $groupId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(string $groupId)
    {
        // Asumiendo que Group tiene relaciones 'members' y 'events'
        $group = Group::with(['members', 'events'])
                      ->where('group_id', $groupId)
                      ->first();

        if (!$group) {
            return redirect()->route('dashboard')->with('error', 'El grupo solicitado no existe.');
        }

        // Comprobación de que el usuario actual es miembro del grupo (o el organizador)
        // Esto es una simplificación, la lógica real requeriría verificar la tabla user_group.
        $isOrganizer = Auth::id() === $group->organizer_id;

        // Simulación de pertenencia si no se implementa user_group por ahora.
        // En una implementación real, se verificaría la tabla pivot USER_GROUP.
        $isMember = $isOrganizer || $group->members->contains(Auth::user());

        if (!$isMember) {
             return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver este grupo.');
        }

        return view('groups.show', compact('group', 'isOrganizer'));
    }
}
