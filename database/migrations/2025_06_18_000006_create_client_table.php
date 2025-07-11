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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('sex');
            $table->integer('age')->unsigned();
            $table->string('address');
            $table->string('contact_number')->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('valid_id')->default(false);

            

            $table->unsignedBigInteger('municipality_id');
            $table->foreign('municipality_id')->references('id')->on('municipalities')->onDelete('cascade');

            $table->unsignedBigInteger('assistance_type_id')->nullable();
            $table->foreign('assistance_type_id')->references('id')->on('assistance_types')->onDelete('set null');

            $table->unsignedBigInteger('assistance_category_id')->nullable();
            $table->foreign('assistance_category_id')->references('id')->on('assistance_categories')->onDelete('set null');

            $table->unsignedBigInteger('assessed_by')->nullable();
            $table->foreign('assessed_by')->references('id')->on('users')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
