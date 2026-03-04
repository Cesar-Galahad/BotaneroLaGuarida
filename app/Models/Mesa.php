<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $table = 'mesas';
    public $timestamps = false;

    protected $fillable = ['numero', 'capacidad', 'estado'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'mesa_id');
    }

    public function pedidoAbierto()
    {
        return $this->hasOne(Pedido::class, 'mesa_id')->where('estado', 'abierto');
    }
}