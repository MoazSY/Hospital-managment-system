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
        Schema::create('patient_bill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounter_id')->references('id')->on('accounter')->onDelete('cascade');
            $table->foreignId('patient_id')->references('id')->on('patient')->onDelete('cascade');
            $table->float('consumers_price');
            $table->float('operation_price');
            $table->float('stay_price');
            $table->float('radiology_report_price');
            $table->float('magnitic_report_price');
            $table->float('laboratory_analysis_price');
            $table->float('doctor_examination_price');
            $table->float('total_bill');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_bill');
    }
};
