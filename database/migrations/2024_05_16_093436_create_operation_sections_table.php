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
        Schema::create('operation_sections', function (Blueprint $table) {
            $table->id();
            $table->string('Section_name');
            $table->foreignId('doctors_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->longText('info_section');
            $table->string('contact_info');
            $table->boolean('available')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_sections');
    }
};
