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
        // Adding indexes for `units` table
        Schema::table('units', function (Blueprint $table) {
            $table->index('property_id');  // Foreign key for property_id
            $table->index('unit_number');  // Index for unit_number for fast lookups of specific unit
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping indexes for `units` table
        Schema::table('units', function (Blueprint $table) {
            $table->dropIndex(['property_id']);
            $table->dropIndex(['unit_number']);
        });
    }
};
