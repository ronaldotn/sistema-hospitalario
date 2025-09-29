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
        Schema::create('patient_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('patient_uuid')->comment('Referencia al paciente principal');
            $table->unsignedInteger('version')->comment('Número de versión del registro');
            $table->json('data')->comment('Snapshot del paciente en JSON');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('Usuario que realizó la actualización');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_histories');
    }
};
