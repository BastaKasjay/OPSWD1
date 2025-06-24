<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('unclaimed_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_payment_id');
            $table->unsignedBigInteger('client_id');
            $table->decimal('amount', 12, 2);
            $table->string('check_number');
            $table->date('date_prepared');
            $table->integer('elapsed_time')->nullable(); // in days or as needed
            $table->timestamps();

            $table->foreign('check_payment_id')->references('id')->on('check_payments')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('unclaimed_checks');
    }
};