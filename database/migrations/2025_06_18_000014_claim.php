<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('client_assistance_id');
            $table->enum('status', ['approved', 'disapproved', 'pending'])->default('pending');
            $table->timestamp('date_status_updated')->nullable();
            $table->string('reason_of_disapprovement')->nullable();
            $table->decimal('amount_approved', 12, 2)->nullable();
            $table->date('date_cafoa_prepared')->nullable();
            $table->date('date_pgo_received')->nullable();
            $table->enum('form_of_payment', ['cheque', 'cash'])->nullable();
            $table->date('payout_date')->nullable();
            $table->enum('source_of_fund', ['Regular', 'Senior', 'PDRRM'])->nullable();
            $table->timestamps();

            // Foreign keys (assume clients and client_assistances tables exist)
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('client_assistance_id')->references('id')->on('client_assistance')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('claims');
    }
};