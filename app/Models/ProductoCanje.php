<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoCanje extends Model
{
    public $timestamps = false;
    protected $table = 'productos_canje';
    protected $fillable = ['producto_id', 'tamanio_id', 'puntos_costo', 'estado'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    public function tamanio()
{
    return $this->belongsTo(Tamanio::class);
}
}