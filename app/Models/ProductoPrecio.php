<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoPrecio extends Model
{
    public $timestamps = false;

    protected $table = 'producto_precios';

    protected $fillable = ['producto_id', 'nombre', 'precio'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}