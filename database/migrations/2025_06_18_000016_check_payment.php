<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('check_payments', function (Blueprint $table) {
            $table->id();
            // CHANGED: Use claims.id as FK, not approved_claims_id
            $table->unsignedBigInteger('claim_id'); // was approved_claims_id
            $table->unsignedBigInteger('client_id');
            $table->date('date_prepared');
            $table->decimal('amount', 12, 2);
            $table->string('check_no')->unique();
            $table->date('date_claimed')->nullable();
            $table->string('status')->default('pending due to payee change');
            $table->timestamps();

            // CHANGED: FK to claims.id
            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('check_payments');
    }
};