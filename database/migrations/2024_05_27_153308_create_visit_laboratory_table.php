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
        Schema::create('visit_laboratory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->foreignId('laboratorys_id')->references('id')->on('laboratorys')->onDelete('cascade');
            $table->date('enterDate');
            $table->time('enterTime')->nullable();
            $table->time('endTime')->nullable();
            $table->string('typeVisit');
            $table->string('section_name');
            $table->string('section_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_laboratory');
    }
};
