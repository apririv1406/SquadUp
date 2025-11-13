<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 3. Usar la función que creamos en el modelo User
        if ($user->isAdmin()) {
            return $next($request); // Continuar si es Admin
        }

        // 4. Si no es Admin, denegar acceso (403 Prohibido)
        return abort(403, 'Acceso denegado. Se requiere ser Administrador.');
    }
}
