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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // El código de barras (ej. código de alumno)
            $table->string('nombre');
            $table->enum('genero', ['hombre', 'mujer', 'otro']);
            $table->string('aula'); // El aula a la que pertenece o donde está asignado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
