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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('unit_number');
            $table->decimal('rent_amount', 10, 2);
            $table->integer('size')->nullable(); // in square feet/meters
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->json('features')->nullable();
            $table->enum('status', ['vacant', 'occupied', 'maintenance'])->default('vacant');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
