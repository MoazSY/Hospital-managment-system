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
        Schema::create('hospital_manager', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->date('birthdate');
            $table->string('phoneNumber');
            $table->longText('about_him');
            $table->string('userName');
            $table->string('password');
            $table->string('email');
            $table->string('address');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_manager');
    }
};
