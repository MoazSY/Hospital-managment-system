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
        Schema::create('request_medical_supplies', function (Blueprint $table) {
            $table->id();
            $table->integer('doctor_id');
            $table->integer('nurse_id')->nullable();
            $table->foreignId('drugs_supplies_id')->references('id')->on('drugs_supplies')->onDelete('cascade');
            $table->integer('quentity');
            $table->foreignId('operation_sections_id')->references('id')->on('operation_sections')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_medical_supplies');
    }
};
