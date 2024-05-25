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
        Schema::create('drugs_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmatical_warehouse_id')->references('id')->on('pharmatical_warehouse')->onDelete('cascade');
            $table->string('name');
            $table->integer('quentity');
            $table->string('category');
            $table->float('price');
            $table->string('manufacture_company');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drugs_supplies');
    }
};
