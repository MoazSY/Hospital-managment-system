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

        Schema::create('consumer_employee', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->date('birthdate');
            $table->text('about_him');
            $table->string('phoneNumber');
            $table->string('userName');
            $table->string('password');
            $table->string('email');
            $table->string('address');
            $table->foreignId('operation_sections_id')->references('id')->on('operation_sections')->onDelete('cascade');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumer_employee');
    }
};
