<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    public $timestamps = false;

    use HasFactory;

    // Define los IDs de rol que se utilizan en la tabla pivote 'user_group'
    // Estos valores deben coincidir con los IDs reales de tu tabla de roles.
    public const ROLE_ADMIN = 1;
    public const ROLE_ORGANIZER = 2;
    public const ROLE_MEMBER = 3;

    // ... (otras propiedades)

    /**
     * Define la clave primaria.
     */
    protected $primaryKey = 'group_id';

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'organizer_id',
        'name',
        'description',
        'invitation_code',
    ];


    // -----------------------------------------------------------------
    // RELACIONES
    // -----------------------------------------------------------------

    /**
     * Los eventos que pertenecen a este grupo.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'group_id', 'group_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'user_group', 'group_id', 'user_id');
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id', 'user_id');
    }
}
