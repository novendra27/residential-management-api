<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_balances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_income', 15, 0)->default(0);
            $table->decimal('total_expense', 15, 0)->default(0);
            $table->decimal('ending_balance', 15, 0)->default(0);
            $table->dateTime('created_at')->useCurrent();

            $table->unique(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_balances');
    }
};
