<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_house_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('resident_id');
            $table->uuid('house_id');
            $table->date('move_in_date');
            $table->date('move_out_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->useCurrent();

            $table->foreign('resident_id')
                ->references('id')->on('residents')
                ->onDelete('cascade');

            $table->foreign('house_id')
                ->references('id')->on('houses')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_house_histories');
    }
};
