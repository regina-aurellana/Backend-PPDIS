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
        Schema::create('dupa_material_per_project_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dupa_per_project_id');
            $table->string('material_note');
            $table->timestamps();

            $table->foreign('dupa_per_project_id')->references('id')->on('dupa_per_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dupa_per_material_notes');
    }
};
