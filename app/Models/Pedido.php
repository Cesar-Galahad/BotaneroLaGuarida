<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'ordenar_pedidos';
    public $timestamps = false;

    protected $fillable = [
        'fecha', 'estado', 'empleado_id', 'cliente_id', 'mesa_id',
    ];

    protected $casts = ['fecha' => 'datetime'];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'pedido_id');
    }
}