<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellidop',
        'apellidom',
        'telefono',
        'imagen',
        'estado',
        'puntos',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellidop} {$this->apellidom}");
    }
}