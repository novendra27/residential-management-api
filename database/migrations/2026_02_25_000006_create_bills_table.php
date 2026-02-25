<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('house_id');
            $table->uuid('resident_id')->nullable();
            $table->uuid('fee_type_id');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('total_amount', 15, 0)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->dateTime('created_at')->useCurrent();

            $table->foreign('house_id')
                ->references('id')->on('houses')
                ->onDelete('cascade');

            $table->foreign('resident_id')
                ->references('id')->on('residents')
                ->onDelete('set null');

            $table->foreign('fee_type_id')
                ->references('id')->on('fee_types')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
