<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->string('estado')->default('abierto');
            $table->foreignId('empleado_id')->nullable()->constrained('empleados');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('mesa_id')->nullable()->constrained('mesas');
        });
    }
    public function down(): void { Schema::dropIfExists('pedidos'); }
};