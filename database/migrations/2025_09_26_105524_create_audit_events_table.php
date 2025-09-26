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
        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();       // UUID público
            $table->string('evento');             // Ej.: "Creación Practitioner"
            $table->string('recurso');            // Ej.: "Practitioner"
            $table->uuid('recurso_uuid');         // UUID del recurso afectado
            $table->json('detalle')->nullable();  // Detalle de cambios (campo, valor antes/después)
            $table->unsignedBigInteger('usuario_id')->nullable(); // ID del usuario que hizo el cambio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_events');
    }
};
