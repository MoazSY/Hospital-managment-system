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
        Schema::create('report_patient_doctor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nurses_id')->references('id')->on('nurses')->onDelete('cascade');
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->foreignId('operation_sections_id')->references('id')->on('operation_sections')->onDelete('cascade');
            $table->string('name_examination');
            $table->integer('id_examination');
            $table->string('status_patient');
            $table->text('explanation');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_patient_doctor');
    }
};
