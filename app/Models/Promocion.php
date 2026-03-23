<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';
    public $timestamps = false;

    protected $fillable = [
        'nombre_p', 'tipo', 'valor',
        'fecha_inicio', 'fecha_fin', 'estado',
    ];

    protected $casts = [
        'valor'        => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'promocion_producto')
                ->withPivot('tamanio_id');
    }
}