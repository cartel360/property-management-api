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
        // Adding indexes for `payments` table
        Schema::table('payments', function (Blueprint $table) {
            $table->index('lease_id');     // Foreign key for lease_id (payment for lease)
            $table->index('payment_date'); // Index for payment_date for faster queries by date range
            $table->index('status');       // Index for payment status (completed, pending)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping indexes for `payments` table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['lease_id']);
            $table->dropIndex(['payment_date']);
            $table->dropIndex(['status']);
        });
    }
};
