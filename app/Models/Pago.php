<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    public $timestamps = false;

    protected $fillable = [
        'pedido_id',
        'metodo_pago',
        'monto',
    ];

    protected $casts = ['monto' => 'decimal:2'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}