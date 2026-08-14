<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')
                ->unique() // ✅ Esto asegura que solo haya una promoción por producto
                ->constrained('productos')
                ->onDelete('cascade');
            $table->string('titulo');
            $table->decimal('descuento', 8, 2)->nullable(); // porcentaje o monto
            $table->date('inicio');
            $table->date('fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};
