<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Redirige al usuario a la página de autenticación de Google.
     * Corresponde a la ruta: GET auth/google/redirect
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect()
    {
        // Hardcodeamos 'google' ya que la ruta no pasa el parámetro dinámico.
        return Socialite::driver('google')->redirect();
    }

    /**
     * Maneja el callback de Google, crea o autentica al usuario.
     * Corresponde a la ruta: GET auth/google/callback
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback()
    {
        // Hardcodeamos 'google' para la lógica interna.
        $provider = 'google';

        try {
            // 1. Obtener la información del usuario del proveedor.
            $socialiteUser = Socialite::driver($provider)->user();

        } catch (\Exception $e) {
            // En caso de error (ej. permisos denegados), redirigir al login.
            // \Log::error("Error de Socialite: " . $e->getMessage());
            return redirect('/login')->with('error', "No se pudo iniciar sesión con Google. Inténtalo de nuevo.");
        }

        // 2. Lógica de búsqueda/creación de usuario
        // Buscamos si existe un usuario con el ID de Google.
        $user = User::where("{$provider}_id", $socialiteUser->getId())->first();

        if ($user) {
            // Usuario encontrado, iniciar sesión.
            Auth::login($user);
            return redirect()->intended('/dashboard'); // O la ruta principal de tu app

        } else {
            // El usuario no existe. Buscar por email y asociar, o crear uno nuevo.
            $existingUserByEmail = User::where('email', $socialiteUser->getEmail())->first();

            if ($existingUserByEmail) {
                // Si existe por email, asociar el ID de Google.
                $existingUserByEmail->{"{$provider}_id"} = $socialiteUser->getId();
                $existingUserByEmail->save();
                Auth::login($existingUserByEmail);
                return redirect()->intended('/dashboard');
            } else {
                // Crear nuevo usuario.
                $newUser = User::create([
                    'role_id' => 3,
                    'name' => $socialiteUser->getName(),
                    'email' => $socialiteUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'created_at' => now(),
                    "{$provider}_id" => $socialiteUser->getId(),
                ]);

                Auth::login($newUser);
                return redirect()->intended('/dashboard');
            }
        }
    }
}
