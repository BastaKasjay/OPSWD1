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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->year('year'); // Example: 2025
            $table->enum('type', ['Regular', 'Senior', 'PDRRM']); // The 3 types
            $table->decimal('allocated_amount', 15, 2); // Budget amount
            $table->timestamps();

            $table->unique(['year', 'type']); // Ensure one budget per year per type
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
