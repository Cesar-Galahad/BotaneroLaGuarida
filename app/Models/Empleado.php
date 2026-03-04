<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Empleado extends Authenticatable
{
    use Notifiable;

    protected $table = 'empleados';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellidop',
        'apellidom',
        'correo',
        'contrasena',
        'estado',
        'imagen',
        'rol_id',
    ];

    protected $hidden = ['contrasena'];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellidop} {$this->apellidom}");
    }
}
