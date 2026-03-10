<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidop')->nullable();
            $table->string('apellidom')->nullable();
            $table->string('telefono')->nullable();
            $table->string('imagen')->nullable();
            $table->string('estado')->default('activo');
            $table->integer('puntos')->default(0);
        });
    }
    public function down(): void { Schema::dropIfExists('clientes'); }
};