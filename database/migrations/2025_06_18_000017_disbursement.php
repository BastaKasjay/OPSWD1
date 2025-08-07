<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('claim_id');
            $table->unsignedBigInteger('client_id');

            $table->unsignedBigInteger('client_assistance_id')->nullable();

            $table->unsignedBigInteger('cash_payment_id')->nullable(); // optional
            $table->unsignedBigInteger('check_payment_id')->nullable(); // new column

            $table->enum('form_of_payment', ['cash', 'cheque'])->nullable(); // new column
            $table->decimal('amount', 12, 2);
            $table->date('payout_date')->nullable();
            $table->date('date_received_claimed')->nullable();
            $table->date('date_released')->nullable();
            $table->enum('claim_status', ['claimed', 'unclaimed', 'pending'])->default('unclaimed');

            $table->timestamps();

            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('client_assistance_id')->references('id')->on('client_assistance')->onDelete('cascade');
            $table->foreign('cash_payment_id')->references('id')->on('cash_payments')->onDelete('set null');
            $table->foreign('check_payment_id')->references('id')->on('check_payments')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('disbursements');
    }
};
