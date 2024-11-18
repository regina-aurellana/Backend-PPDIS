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
        Schema::create('table_dupa_component_formulas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_dupa_component_id');
            $table->unsignedBigInteger('formula_id');
            $table->timestamps();

            $table->foreign('table_dupa_component_id')->references('id')->on('table_dupa_components');
            $table->foreign('formula_id')->references('id')->on('formulas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_dupa_component_formulas');
    }
};
