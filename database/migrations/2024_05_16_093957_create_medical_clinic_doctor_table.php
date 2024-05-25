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
        Schema::create('medical_clinic_doctor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_clinic_id')->references('id')->on('medical_clinic')->onDelete('cascade');
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->float('price');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('days');//array
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_clinic_doctor');
    }
};
