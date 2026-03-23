<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialContrasena extends Model
{
    public $timestamps = false;
    protected $table = 'historial_contrasenas';
    protected $fillable = ['empleado_id', 'contrasena'];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}