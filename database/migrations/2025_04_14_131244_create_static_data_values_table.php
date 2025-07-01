<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('static_data_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('static_data_id');
            $table->mediumText('data')->nullable();
            $table->text('options')->nullable();
            $table->string('lang')->nullable();
            $table->timestamps();

            $table->foreign('static_data_id')
                ->references('id')
                ->on('static_data')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_data_values');
    }
};
