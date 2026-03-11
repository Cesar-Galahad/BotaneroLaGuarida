<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio_base', 10, 2);
            $table->integer('existencia')->default(0);
            $table->string('imagen')->nullable();
            $table->string('estado')->default('activo');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias');
        });
    }
    public function down(): void { Schema::dropIfExists('productos'); }
};