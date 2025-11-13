<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    public $timestamps = false;
    
    use HasFactory;

    // Asumiendo que el campo clave primaria se llama 'expense_id'
    protected $primaryKey = 'expense_id';

    protected $fillable = [
        'event_id',
        'payer_id',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2', // Asegura que el monto sea un decimal con 2 posiciones
    ];

    // --- RELACIONES ---

    /**
     * Relación: El evento al que pertenece este gasto.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    /**
     * Relación: El usuario que pagó este gasto.
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id', 'user_id');
    }
}
