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
     * Redirige al usuario a Google.
     */
    public function redirect()
    {
        // Sin scopes explícitos
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback de Google.
     */
    public function callback()
    {
        $provider = 'google';

        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with(
                'error',
                "No se pudo iniciar sesión con Google. Inténtalo de nuevo."
            );
        }

        $user = User::where("{$provider}_id", $socialiteUser->getId())->first();

        if ($user) {
            Auth::login($user);
            return redirect()->intended('/dashboard');
        }

        $existingUserByEmail = User::where('email', $socialiteUser->getEmail())->first();

        if ($existingUserByEmail) {
            $existingUserByEmail->{"{$provider}_id"} = $socialiteUser->getId();
            $existingUserByEmail->save();

            Auth::login($existingUserByEmail);
            return redirect()->intended('/dashboard');
        }

        $newUser = User::create([
            'role_id' => 3,
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'password' => Hash::make(Str::random(16)),
            "{$provider}_id" => $socialiteUser->getId(),
        ]);

        Auth::login($newUser);
        return redirect()->intended('/dashboard');
    }
}
