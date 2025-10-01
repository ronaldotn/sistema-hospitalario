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
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('encounter_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('code', 50)->nullable()->comment('LOINC');
            $table->text('value')->nullable();
            $table->string('unit', 20)->nullable();
            $table->timestamp('observed_at')->useCurrent();

            $table->foreign('encounter_id')
                  ->references('id')
                  ->on('encounters')
                  ->onDelete('cascade');

            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patients')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};
