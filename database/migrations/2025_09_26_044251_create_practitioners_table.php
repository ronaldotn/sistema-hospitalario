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
        Schema::create('practitioners', function (Blueprint $table) {
            $table->id();                                // PK interna para Laravel
            $table->uuid('uuid')->unique();              // Identificador público FHIR
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('especialidad')->nullable();
            $table->string('nro_colegiado')->unique();
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practitioners');
    }
};
