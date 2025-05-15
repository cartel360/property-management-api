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
        // Adding indexes for `users` table
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');        // Index for email for fast lookups
            $table->index('role');         // Index for role to filter by user type (admin, agent, landlord)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping indexes for `users` table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['role']);
        });
    }
};
