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
        Schema::create('assistance_categories', function (Blueprint $table) {
            $table->id();
            // Foreign key to the assistance_type table
            $table->unsignedBigInteger('assistance_type_id');
            $table->string('category_name')->unique();
            $table->timestamps();


            // Foreign key constraint
            $table->foreign('assistance_type_id')
                  ->references('id')->on('assistance_types')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistance_categories');
    }
};
