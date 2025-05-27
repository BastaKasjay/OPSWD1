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
        Schema::create('client', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('relationship');
            $table->string('sex');
            $table->integer('age')->unsigned();
            $table->boolean('4ps')->default(false);
            $table->boolean('pwd')->default(false);
            $table->string('address');
            $table->string('contact_number');
            $table->unsignedBigInteger('valid_id');
            $table->string('assessed_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client');
    }
};
