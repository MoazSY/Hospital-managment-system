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
        Schema::create('consumers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->foreignId('drugs_supplies_id')->references('id')->on('drugs_supplies')->onDelete('cascade');
            $table->foreignId('consumer_employee_id')->references('id')->on('consumer_employee')->onDelete('cascade');
            $table->foreignId('request_medical_supplies_id')->references('id')->on('request_medical_supplies')->onDelete('cascade');
            $table->foreignId('medical_operation_id')->references('id')->on('medical_operation')->onDelete('cascade');
            $table->integer('quentity');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumers');
    }
};
