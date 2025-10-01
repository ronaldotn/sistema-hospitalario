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
        Schema::create('diagnostic_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('encounter_id')->nullable();
            $table->string('type', 100)->nullable();
            $table->text('result')->nullable();
            $table->json('document')->nullable()->comment('FHIR DiagnosticReport');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patients')
                  ->onDelete('cascade');

            $table->foreign('encounter_id')
                  ->references('id')
                  ->on('encounters')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_reports');
    }
};
