<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SocialiteController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminEventController;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

Route::get('/debug-users', function () {
    return DB::select("
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns
        WHERE table_name = 'users'
        ORDER BY ordinal_position
    ");
});

Route::get('/debug-roles-columns', function () {
    return DB::select("
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns
        WHERE table_name = 'roles'
        ORDER BY ordinal_position
    ");
});

Route::get('/debug-roles', function () {
    return DB::table('roles')->get();
});


Route::get('/debug-users-data', function () {
    return DB::table('users')->get();
});

Route::get('/debug-groups-data', function () {
    return DB::table('groups')->get();
});

Route::get('/debug-groups-columns', function () {
    return DB::select("
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns
        WHERE table_name = 'groups'
        ORDER BY ordinal_position
    ");
});

Route::get('/debug-usergroups-columns', function () {
    return DB::select("
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns
        WHERE table_name = 'user_group'
        ORDER BY ordinal_position;

    ");
});

Route::get('/debug-usergroups-data', function () {
    return DB::table('user_group')->get();
});


// Rutas de Socialite (Autenticación con Google)
Route::get('login/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('login/auth/google/callback', [SocialiteController::class, 'callback']);
Route::get('/login/auth/google', [SocialiteController::class, 'redirect'])->name('google.login');

// Página de inicio
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// --- GRUPO DE RUTAS PROTEGIDAS POR AUTENTICACIÓN ---
Route::middleware('auth')->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Eventos
    Route::get('/explore', [EventController::class, 'explore'])->name('events.explore');
    Route::get('/events/create', [EventController::class, 'create'])->name('event.create');
    Route::post('/events', [EventController::class, 'store'])->name('event.store');
    Route::get('/events/{event}/show', [EventController::class, 'show'])->name('event.show');
    Route::post('/events/{event}/expense', [ExpenseController::class, 'store'])->name('event.store_expense');
    Route::delete('/events/{event}/expense/{expense}', [ExpenseController::class, 'destroy'])->name('event.expense.destroy');
    Route::post('/events/{event}/join', [EventController::class, 'join'])->name('event.join');
    Route::post('/events/{event}/leave', [EventController::class, 'leave'])->name('event.leave');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('event.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('event.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('event.destroy');
    Route::post('/events/{event}/expense/{expense}/settle', [EventController::class, 'settle'])->name('event.expense.settle');

    // Grupos
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    // Unirse a un grupo mediante código
    Route::post('/groups/join', [GroupController::class, 'joinByCode'])->name('groups.joinByCode');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');


    // Administración (solo Admin)
    Route::middleware(['is_admin'])
        ->prefix('admin')
        ->as('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
            Route::resource('users', AdminUserController::class);
            Route::resource('events', AdminEventController::class);
        });
});


require __DIR__ . '/auth.php';
