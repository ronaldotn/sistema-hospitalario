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
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('encounter_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('code', 50)->nullable()->comment('ICD-10 o SNOMED');
            $table->text('description')->nullable();
            $table->timestamp('recorded_date')->useCurrent();

            $table->foreign('encounter_id')
                  ->references('encounter_id')
                  ->on('encounters')
                  ->onDelete('cascade');

            $table->foreign('patient_id')
                  ->references('patient_id')
                  ->on('patients')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};
