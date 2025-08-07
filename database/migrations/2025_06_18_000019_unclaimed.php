<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('unclaimed', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('cascade');

            $table->foreignId('cash_payment_id')
                ->nullable()
                ->constrained('cash_payments')
                ->onDelete('cascade');

            $table->foreignId('check_payment_id')
                ->nullable()
                ->constrained('check_payments')
                ->onDelete('cascade');

            $table->decimal('amount', 12, 2);
            $table->string('check_number')->nullable(); // optional, in case it's cash
            $table->date('date_prepared');
            $table->integer('elapsed_time')->nullable(); // in days or as needed

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('unclaimed');
    }
};
