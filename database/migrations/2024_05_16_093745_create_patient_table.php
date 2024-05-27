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

        Schema::create('patient', function (Blueprint $table) {
            $table->id();
            $table->string('id_file')->unique();
            $table->string('name');
            $table->string('gender');
            $table->date('birthdate');
            $table->string('birth_address');
            $table->string('cur_address');
            $table->string('phoneNumber');
            $table->string('phoneNumber_near');
            $table->string('info_health_insurance')->nullable();
            $table->string('NumberDocument_ins')->nullable();
            $table->text('details_covering_ins')->nullable();
            $table->text('condition_ins')->nullable();
            $table->text('contact_info_companany')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient');
    }
};
