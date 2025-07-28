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
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();

            // Foreign key to the assistance_type table
            $table->unsignedBigInteger('assistance_type_id');
            $table->string('requirement_name', 512);
            $table->text('value')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('assistance_type_id')
                  ->references('id')->on('assistance_types')
                  ->onDelete('cascade');

            // Composite unique constraint
            $table->unique(['assistance_type_id', 'requirement_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
