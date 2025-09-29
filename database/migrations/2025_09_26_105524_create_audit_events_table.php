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
            $table->uuid('uuid')->unique()->comment('Identificador único del evento');
            $table->string('evento')->comment('Tipo de acción: create, update, delete');
            $table->string('recurso')->comment('Tabla o entidad afectada');
            $table->uuid('recurso_uuid')->nullable()->comment('UUID del recurso afectado');
            $table->json('detalle')->nullable()->comment('Detalles de la acción en JSON');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete()->comment('Usuario que realizó la acción');
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
