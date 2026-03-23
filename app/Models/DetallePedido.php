<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido';
    public $timestamps = false;

    protected $fillable = [
        'pedido_id',
        'producto_id', 
        'tamanio_id',
        'cantidad',
        'precio_unitario',
        'descuento_aplicado',
    ];

    protected $casts = [
        'precio_unitario'    => 'decimal:2',
        'descuento_aplicado' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function tamanio()
    {
        return $this->belongsTo(Tamanio::class, 'tamanio_id');
    }
}