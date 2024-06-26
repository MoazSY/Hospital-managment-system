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
        Schema::create('operation_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_sections_id')->references('id')->on('operation_sections')->onDelete('cascade');
            $table->string('numberRoom');
            $table->boolean('available')->nullable();
            $table->float('hour_price_stay');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_rooms');
    }
};
