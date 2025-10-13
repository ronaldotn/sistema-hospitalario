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
        DB::statement("
            CREATE OR REPLACE VIEW vw_practitioners_lookup AS
            SELECT 
                id,
                CONCAT(first_name, ' ', last_name) AS full_name,
                specialty
            FROM practitioners
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vw_practitioners_lookup_view');
    }
};
