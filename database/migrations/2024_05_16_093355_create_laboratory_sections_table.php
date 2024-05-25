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
        Schema::create('laboratory_sections', function (Blueprint $table) {
            $table->id();
            $table->string('type_laboratory');
            $table->string('address');
            $table->string('contact_info');
            $table->text('about_him');
            $table->foreignId('laboratorys_id')->references('id')->on('laboratorys')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_sections');
    }
};
