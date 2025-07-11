<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payees', function (Blueprint $table) {
            $table->id();

            // Foreign Key to clients table
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            // Reference to previous payee (if needed)
            $table->unsignedBigInteger('previous_payee_id')->nullable();
            $table->foreign('previous_payee_id')->references('id')->on('payees')->onDelete('set null');

            // Payee Details — make nullable to support self-payees
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('relationship')->nullable();
            $table->string('proof_of_relationship')->nullable();
            $table->boolean('valid_id')->default(false);

            // Boolean Flags
            $table->boolean('updated_to_new_payee')->default(false);
            $table->boolean('is_self_payee')->default(false); // ✅ Track self-payees

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payees');
    }
};
