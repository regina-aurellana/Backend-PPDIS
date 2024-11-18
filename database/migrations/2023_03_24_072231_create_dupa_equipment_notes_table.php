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
        Schema::create('dupa_equipment_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dupa_id');
            $table->string('equipment_note');
            $table->timestamps();

            $table->foreign('dupa_id')->references('id')->on('dupas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dupa_equipment_notes');
    }
};
