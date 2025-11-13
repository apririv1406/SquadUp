<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Group;
use App\Models\Attendance;
use App\Models\Expense;

class Event extends Model
{
    public $timestamps = false;
    
    use HasFactory;

    /**
     * La clave primaria asociada con la tabla (según tu ER).
     */
    protected $primaryKey = 'event_id';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'group_id',
        'title',
        'sport_name',
        'location',
        'event_date',
        'is_public',
        'capacity',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos.
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'is_public' => 'boolean',
            'capacity' => 'integer',
        ];
    }

    // -----------------------------------------------------------------
    // RELACIONES
    // -----------------------------------------------------------------


    /**
     * Define la relación: El evento pertenece a un creador (usuario).
     */
    public function creator(): BelongsTo
    {
        // Asumiendo que el campo de clave foránea es 'creator_id' en la tabla 'events',
        // y la clave local es 'user_id' en la tabla 'users'.
        return $this->belongsTo(User::class, 'creator_id', 'user_id');
    }

    /**
     * Define la relación con el grupo.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    /**
     * Los gastos asociados a este evento. (1:N)
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'event_id');
    }

    /**
     * Los registros de asistencia (pivote) para este evento.
     * ESTO SOLUCIONA EL ERROR: Call to undefined relationship [attendance]
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'event_id', 'event_id');
    }

    /**
     * Los usuarios (modelos User) que asisten a este evento (confirmados o no).
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendance', 'event_id', 'user_id')
                    ->using(Attendance::class) // Usa el modelo pivote
                    ->withPivot('is_confirmed', 'confirmation_date'); // Carga los campos extra
    }
}
