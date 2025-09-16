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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // El identificador único universal (UUID)
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('documento_identidad')->unique();
            $table->date('fecha_nacimiento');
            $table->string('sexo');
            $table->string('direccion')->nullable();
            $table->string('contacto')->nullable();
            $table->string('correo')->unique();
            // ¡Aquí viene la mejora!
            $table->json('fhir_identifier')->nullable(); // Campo JSON para el identificador FHIR
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
