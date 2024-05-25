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

        Schema::create('result_laboratory_anylysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratorys_id')->references('id')->on('laboratorys')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->foreignId('laboratory_anylysis_id')->references('id')->on('laboratory_anylysis')->onDelete('cascade');
            $table->date('result_date');
            $table->string('result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_laboratory_anylysis');
    }
};
