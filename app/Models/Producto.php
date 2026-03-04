<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_base',
        'existencia',
        'imagen',
        'estado',
        'categoria_id',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function promociones()
    {
        return $this->belongsToMany(
            Promocion::class, 'promocion_producto', 'producto_id', 'promocion_id'
        );
    }

    public function detallesPedido()
    {
        return $this->hasMany(DetallePedido::class, 'producto_id');
    }
}