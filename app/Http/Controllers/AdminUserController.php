<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Validación de datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'role_id' => 'required|integer|in:' . implode(',', [
                User::ROLE_ADMIN,
                User::ROLE_ORGANIZER,
                User::ROLE_MEMBER,
            ]),
        ]);

        // Actualizar usuario
        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Buscar gastos asociados al usuario
        $expenses = \App\Models\Expense::where('payer_id', $user->user_id)->get();

        if ($expenses->isNotEmpty()) {
            // Eliminar todos los gastos asociados (liquidados o no)
            foreach ($expenses as $expense) {
                $expense->delete();
            }
        }

        // Ahora sí se puede eliminar el usuario
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario y sus gastos asociados eliminados correctamente.');
    }
}
