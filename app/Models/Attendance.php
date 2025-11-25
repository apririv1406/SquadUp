<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Representa la tabla pivote 'attendance' con campos adicionales.
 * Extiende de Pivot para ser usada correctamente en las relaciones BelongsToMany::using().
 * Contiene las claves compuestas user_id y event_id.
 */
class Attendance extends Pivot
{
    // Nombre de la tabla pivote
    protected $table = 'attendance';
    public $timestamps = false;

    // Las claves primarias son compuestas (user_id y event_id)
    // No tiene clave primaria incremental
    protected $primaryKey = ['user_id', 'event_id'];
    public $incrementing = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'is_confirmed',
        'confirmation_date',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_confirmed' => 'boolean',
        'confirmation_date' => 'datetime',
    ];

    // --- RELACIONES ---

    /**
     * Relación: El usuario asociado a esta asistencia.
     * * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relación: El evento asociado a esta asistencia.
     * * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }


    public function confirm()
    {
        $this->is_confirmed = true;
        $this->confirmation_date = now();
        $this->save();
    }

    public function cancel()
    {
        $this->is_confirmed = false;
        $this->save();
    }
}
