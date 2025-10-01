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
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('practitioner_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->timestamp('encounter_date')->useCurrent();
            $table->string('encounter_type', 50)->nullable()->comment('consulta, emergencia, hospitalización');
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('in-progress');

            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patients')
                  ->onDelete('cascade');

            $table->foreign('practitioner_id')
                  ->references('id')
                  ->on('practitioners')
                  ->onDelete('set null');

            $table->foreign('organization_id')
                  ->references('organization_id')
                  ->on('organizations')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
