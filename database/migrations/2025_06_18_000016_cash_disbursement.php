<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cash_disbursements', function (Blueprint $table) {
            $table->id();
            // CHANGED: Use cash_payments.id as FK, not cash_payment_id
            $table->unsignedBigInteger('cash_payment_id'); // was cash_payment_id
            $table->unsignedBigInteger('client_id');
            $table->decimal('amount', 12, 2);
            $table->date('confirmation_date')->nullable();
            $table->date('date_received_claimed')->nullable();
            $table->date('date_released')->nullable();
            $table->decimal('total_amount_claimed', 12, 2)->nullable();
            $table->timestamps();

            // CHANGED: FK to cash_payments.id
            $table->foreign('cash_payment_id')->references('id')->on('cash_payments')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('cash_disbursements');
    }
};