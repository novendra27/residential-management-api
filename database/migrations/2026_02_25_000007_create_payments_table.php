<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bill_id');
            $table->date('payment_date');
            $table->decimal('amount_paid', 15, 0);
            $table->text('notes')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->foreign('bill_id')
                ->references('id')->on('bills')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
