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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_comercial');
            $table->string('slug')->unique();
            $table->string('modelo')->nullable();
            $table->string('item')->nullable();
            $table->foreignId('categoria_id')->constrained();
            $table->foreignId('marca_id')->constrained();
            $table->boolean('destacado')->default(false);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 15, 2)->nullable();
            $table->json('galeria_imagenes')->nullable();
            $table->string('imagen_slug')->nullable();
            $table->string('medidas')->nullable();
            $table->string('peso')->nullable();
            $table->string('industria')->nullable();
            $table->string('tipo')->nullable();
            $table->string('ficha')->nullable();
            $table->string('manual')->nullable();
            $table->boolean('activo')->default(true);
            $table->decimal('orden', 11)->nullable(); // corregido
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
