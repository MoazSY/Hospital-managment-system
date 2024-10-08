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
        Schema::create('medical_operation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operations_id')->references('id')->on('operations')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status_operation');
            $table->text('recomendation');
            $table->json('id_drugs')->nullable();//array
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_operation');
    }
};
