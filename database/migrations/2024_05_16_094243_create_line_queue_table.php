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
        Schema::create('line_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->string('num_char');
            $table->integer('position')->nullable();
            $table->string('section_name');
            $table->integer('section_id');
            $table->integer('visit_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_queue');
    }
};
