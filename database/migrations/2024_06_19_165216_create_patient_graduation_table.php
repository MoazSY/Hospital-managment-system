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
        Schema::create('patient_graduation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->date('out_date');
            $table->time('out_time');
            $table->text('recomendation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_graduation');
    }
};
