<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    // Clave Primaria según script SQL
    protected $primaryKey = 'role_id';

    // Desactivamos los timestamps si la tabla no los tiene
    public $timestamps = false;

    protected $fillable = [
        'name', // Ej. 'Administrador', 'Estándar'
    ];

    // --- RELACIONES ---

    /**
     * Un Rol tiene muchos Usuarios.
     * Necesario para obtener todos los usuarios con un rol específico.
     * Relación Uno a Muchos (1:N).
     */
    public function users()
    {
        // La FK en la tabla 'users' es 'role_id'
        return $this->hasMany(User::class, 'role_id');
    }
}
