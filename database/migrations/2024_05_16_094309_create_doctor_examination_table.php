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
        Schema::create('doctor_examination', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->integer('section_id');
            $table->string('section_name');
            $table->text('medical_history')->nullable();
            $table->string('previous_illnesses')->nullable();//array
            $table->text('Current_symptoms');//array
            $table->date('Symptoms_appear');
            $table->string('Medications_taken')->nullable();//array
            $table->json('id_medical_examination')->nullable();//array
            $table->json('laboratory_analysis')->nullable();//array
            $table->boolean('ask_radiation_image')->nullable();
            $table->string('placeRadiation')->nullable();
            $table->boolean('ask_magnetic_image')->nullable();
            $table->string('place_magnetic')->nullable();
            $table->boolean('ask_operationAction')->nullable();
            $table->string('NameActionOperation')->nullable();
            $table->json('drugs_id')->nullable();//array
            $table->text('result_examination');
            $table->text('medical_recomendation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_examination');
    }
};
