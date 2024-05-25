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

        Schema::create('laboratory_anylysis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('masurement_unit');
            $table->string('natural_limit');
            $table->string('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_anylysis');
    }
};
