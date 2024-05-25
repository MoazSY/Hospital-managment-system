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
        Schema::create('doctor_operation_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreignId('operation_sections_id')->references('id')->on('operation_sections')->onDelete('cascade');
            $table->time('startWorkTime');
            $table->time('endWorkTime');
            $table->string('days');//array
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_operation_section');
    }
};
