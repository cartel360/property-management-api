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
        // Adding index for `tenants` table
        Schema::table('tenants', function (Blueprint $table) {
            $table->index('email');        // Index for email (fast lookup for tenant)
            $table->index('date_of_birth'); // Index for date_of_birth (if querying tenants by age)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping indexes for `tenants` table
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['date_of_birth']);
        });
    }
};
