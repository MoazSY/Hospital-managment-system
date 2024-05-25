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
        Schema::create('pharmatical_warehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignid('warehouse_manager_id')->references('id')->on('warehouse_manager')->onDelete('cascade');
            $table->string('address');
            $table->string('contact_info');
            $table->text('details_info');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmatical_warehouse');
    }
};
