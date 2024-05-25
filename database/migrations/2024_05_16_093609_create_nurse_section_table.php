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
        Schema::create('nurse_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nurses_id')->references('id')->on('nurses')->onDelete('cascade');
            $table->foreignId('operation_sections_id')->references('id')->on('operation_sections')->onDelete('cascade');
            $table->time('startTime');
            $table->time('endTime');
            $table->string('days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_section');
    }
};
