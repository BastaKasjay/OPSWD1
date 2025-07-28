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
        Schema::create('client_assistance', function (Blueprint $table) {
            $table->id();

            //foreign keys
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('assistance_type_id');
            $table->unsignedBigInteger('assistance_category_id');
            $table->string('other_category_name')->nullable();
            $table->unsignedBigInteger('payee_id')->nullable(); // Payee ID can be null if no payee is assigned

            // Request Details
            $table->date('date_received_request');
            $table->string('medical_case')->nullable();
        

            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('assistance_type_id')->references('id')->on('assistance_types')->onDelete('cascade');
            $table->foreign('assistance_category_id')->references('id')->on('assistance_categories')->onDelete('cascade');
            $table->foreign('payee_id')->references('id')->on('payees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_assistance');
    }
};
