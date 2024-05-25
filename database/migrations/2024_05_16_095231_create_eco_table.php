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
        Schema::create('eco', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_examination_id')->references('id')->on('medical_examination')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->date('date');
            $table->string('image_eco');
            $table->string('mitral_value');
            $table->string('aortic_value');
            $table->string('tricuspid_valve');
            $table->string('pulmonary_valve');
            $table->string('left_artial_valve');
            $table->string('hijab_among_ears');
            $table->string('interventricular_diaphragm');
            $table->string('left_ventricle');
            $table->string('right_ventricle');
            $table->string('pericardium');
            $table->string('result_eco');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eco');
    }
};
