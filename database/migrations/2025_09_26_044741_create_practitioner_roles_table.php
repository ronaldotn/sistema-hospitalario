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
        Schema::create('practitioner_roles', function (Blueprint $table) {
            $table->id();                                // PK interna
            $table->foreignId('practitioner_id')         // FK interna
                ->constrained('practitioners')
                ->onDelete('cascade');
            $table->uuid('uuid')->unique();              // UUID público opcional
            $table->string('rol');
            $table->foreignUuid('organizacion_id')->nullable();
            $table->json('permisos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practitioner_roles');
    }
};
