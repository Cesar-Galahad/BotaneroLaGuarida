<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_p');
            $table->string('tipo');
            $table->decimal('valor', 10, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado')->default('activo');
        });

        Schema::create('promocion_producto', function (Blueprint $table) {
            $table->foreignId('promocion_id')->constrained('promociones');
            $table->foreignId('producto_id')->constrained('productos');
            $table->primary(['promocion_id', 'producto_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('promocion_producto');
        Schema::dropIfExists('promociones');
    }
};