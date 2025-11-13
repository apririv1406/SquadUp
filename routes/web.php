<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// Rutas de Socialite (Autenticación con Google)
// Estas rutas deben estar fuera del middleware 'auth'
Route::get('login/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('login/auth/google/callback', [SocialiteController::class, 'callback']);
// Es la URL a la que apunta el botón "Continuar con Google".
Route::get('/login/auth/google', [SocialiteController::class, 'redirect'])->name('google.login');


// --- RUTA PÚBLICA (Solo se usa si necesitas una página de bienvenida separada, si no, se puede borrar) ---
// Normalmente, si el usuario NO está autenticado, esta ruta redirige a 'login'.
// Si el usuario está autenticado, debería ser redirigido a '/dashboard'.
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');


// --- GRUPO DE RUTAS PROTEGIDAS POR AUTENTICACIÓN ---
Route::middleware('auth')->group(function () {

    // 1. DASHBOARD PRINCIPAL (RF10, RF6)
    // Se utiliza la ruta /dashboard como el punto de acceso principal.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. Rutas de Eventos (RF5, RF6, RF7)
    // Vista de Exploración
    Route::get('/explore', [EventController::class, 'explore'])->name('events.explore');

    // Crear Evento (Necesario para el botón de acción rápida)
    Route::get('/events/create', [EventController::class, 'create'])->name('event.create');

    // Detalle y Liquidación de Evento (RF5, RF6)
    Route::get('/events/{event}/show', [EventController::class, 'show'])->name('event.show');

    // Registro de Gasto (RF5)
    Route::post('/events/{event}/expense', [ExpenseController::class, 'store'])->name('event.store_expense');

    // Acción para unirse a un evento público
    Route::post('/events/{event}/join', [EventController::class, 'join'])->name('event.join');
    // Acción para dejar un evento
    Route::post('/events/{event}/leave', [EventController::class, 'leave'])->name('event.leave');
    // Muestra el formulario
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('event.edit');
    // Procesa la actualización
    Route::put('/events/{event}', [EventController::class, 'update'])->name('event.update');
    // Elimina el evento
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('event.destroy');

    // 3. Rutas de Grupos (RF2, RF14)
    Route::prefix('groups')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('groups.index'); // Añadido para el botón "Mis Grupos"
        Route::get('/create', [GroupController::class, 'create'])->name('groups.create');
        Route::post('/', [GroupController::class, 'store'])->name('groups.store');
    });
    Route::get('/groups/{group}', [GroupController::class, 'show'])
        ->name('groups.show');

    // 4. Rutas de Administración (Protegidas por Middleware - RNF3)
    // Asumo que el middleware 'admin' ya verifica también la autenticación.
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        // Ruta de prueba para verificar el rol de Admin
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});


require __DIR__.'/auth.php';
