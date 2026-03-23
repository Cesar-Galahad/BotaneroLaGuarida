<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tamanio extends Model
{
    public $timestamps = false;
    protected $table = 'tamanios';
    protected $fillable = ['cantidad', 'unidad', 'estado'];

    public function getNombreCompletoAttribute()
    {
        return $this->cantidad . ' ' . $this->unidad;
    }

    public function productosPrecios()
    {
        return $this->hasMany(ProductoPrecio::class);
    }
}