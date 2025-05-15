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
        // Adding indexes for `leases` table
        Schema::table('leases', function (Blueprint $table) {
            $table->index('tenant_id');    // Foreign key for tenant_id
            $table->index('unit_id');      // Foreign key for unit_id
            $table->index('start_date');   // Index for start_date for faster queries by start date
            $table->index('end_date');     // Index for end_date for faster queries by end date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping indexes for `leases` table
        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['unit_id']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
        });
    }
};
