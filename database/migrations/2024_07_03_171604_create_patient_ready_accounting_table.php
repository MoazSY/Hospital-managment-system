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
        Schema::create('patient_ready_accounting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->references('id')->on('patient_id')->onDelete('cascade');
            $table->foreignId('consumer_employee_id')->references('id')->on('consumer_employee')->onDelete('cascade');
            $table->foreignId('accounter_id')->references('id')->on('accounter')->onDelete('cascade');
            $table->foreignId('medical_operation_id')->references('id')->on('medical_operation')->onDelete('cascade');
            $table->json('consumers_id');
            $table->boolean('accounting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_ready_accounting');
    }
};
