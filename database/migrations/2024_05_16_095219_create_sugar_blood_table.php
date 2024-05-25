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
        Schema::create('sugar_blood', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_examination_id')->references('id')->on('medical_examination')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->integer('id_nurse')->nullable();
            $table->integer('id_doctor')->nullable();
            $table->date('date');
            $table->string('blood_sugar');
            $table->string('result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sugar_blood');
    }
};
