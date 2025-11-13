<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Event;
use App\Models\Group;
use App\Models\Attendance;
use App\Models\Expense;

class User extends Authenticatable
{

    public $timestamps = false;

    use HasFactory, Notifiable;

    /**
     * La clave primaria asociada con la tabla (según tu ERD).
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // -----------------------------------------------------------------
    // RELACIONES
    // -----------------------------------------------------------------

    /**
     * Define la relación de muchos a muchos con Group.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'user_group', 'user_id', 'group_id');
    }


        /**
     * Eventos a los que asiste el usuario.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'attendance', 'user_id', 'event_id')
                    ->withPivot('is_confirmed', 'confirmation_date')
                    ->withTimestamps();
    }

    /**
     * Eventos que el usuario ha creado (asumiendo que hay un 'creator_id' o 'user_id' en la tabla events).
     */
    public function createdEvents()
    {
        return $this->hasMany(Event::class, 'creator_id', 'user_id');
    }

    /**
     * Los gastos que este usuario ha pagado.
     */
    public function expensesPaid(): HasMany
    {
        return $this->hasMany(Expense::class, 'payer_id', 'user_id');
    }

    /**
     * LOS EVENTOS a los que el usuario asiste (confirmados).
     * Esta es la relación que faltaba y que 'DashboardController' necesita (RF10).
     */
    public function eventsAttending(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'attendance', 'user_id', 'event_id')
                    ->using(Attendance::class) // Especifica el modelo pivote
                    ->wherePivot('is_confirmed', true) // Solo eventos confirmados (RF4)
                    ->withPivot('confirmation_date');
    }

    /**
     * Relación con la tabla pivote 'attendance' (para todos los eventos, confirmados o no).
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    // -----------------------------------------------------------------
    // LÓGICA DE PERMISOS (Corregida)
    // -----------------------------------------------------------------

    /**
     * Verifica si el usuario tiene uno de los roles especificados en un grupo dado.
     * Esta implementación es más robusta y menos propensa a errores de alias de Eloquent.
     *
     * @param int $groupId El ID del grupo.
     * @param array $roleIds Array de IDs de rol (ej: [Group::ROLE_ADMIN, Group::ROLE_ORGANIZER]).
     * @return bool
     */
    public function hasRoleInGroup(int $groupId, array $roleIds): bool
    {
        return $this->groups()
                    ->where('groups.group_id', $groupId) // Filtra el grupo objetivo en la tabla 'groups'
                    ->exists();
    }
}
