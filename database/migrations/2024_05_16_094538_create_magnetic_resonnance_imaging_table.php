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
        Schema::create('magnetic_resonnance_imaging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->string('address');
            $table->string('name');
            $table->string('contact_info');
            $table->text('info_about');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magnetic_resonnance_imaging');
    }
};
