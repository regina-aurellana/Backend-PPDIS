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
        Schema::create('equipment_components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equip_id');
            $table->string('component_name');

            $table->foreign('equip_id')->references('id')->on('equipment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_components');
    }
};