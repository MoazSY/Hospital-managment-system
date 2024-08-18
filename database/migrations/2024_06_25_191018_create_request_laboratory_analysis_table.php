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
        Schema::create('request_laboratory_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->foreignId('laboratory_anylysis_id')->references('id')->on('laboratory_anylysis')->onDelete('cascade');
            $table->string('section_name');
            $table->string('section_id');
            $table->date('date');
            $table->boolean('status_request');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_laboratory_analysis');
    }
};
