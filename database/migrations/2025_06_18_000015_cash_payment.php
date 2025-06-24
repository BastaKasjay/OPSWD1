<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cash_payments', function (Blueprint $table) {
            $table->id();
            // CHANGED: Use claims.id as FK, not approved_claims_id
            $table->unsignedBigInteger('claim_id'); // was approved_claims_id
            $table->unsignedBigInteger('client_id');
            $table->date('date_prepared');
            $table->json('confirmed_people')->nullable(); // list of people who confirmed today
            $table->decimal('amount_confirmed', 12, 2)->nullable();
            $table->decimal('total_amount_withdrawn', 12, 2)->nullable();
            $table->date('date_of_payout')->nullable();
            $table->timestamps();

            // CHANGED: FK to claims.id
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('cash_payments');
    }
};