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
        // Adding indexes for `properties` table
        Schema::table('properties', function (Blueprint $table) {
            $table->index('landlord_id');  // Foreign key for landlord_id
            $table->index('city');         // Index for city for faster search by city
            $table->index('state');        // Index for state for faster search by state
            $table->index('zip_code');     // Index for zip code for faster search by zip code
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping indexes for `properties` table
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['landlord_id']);
            $table->dropIndex(['city']);
            $table->dropIndex(['state']);
            $table->dropIndex(['zip_code']);
        });
    }
};
