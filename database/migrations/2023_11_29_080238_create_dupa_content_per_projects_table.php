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
        Schema::create('dupa_content_per_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dupa_per_project_id');
            $table->string('minor_tool_percentage')->nullable();
            $table->string('consumable_percentage')->nullable();
            $table->string('material_area')->nullable();
            $table->string('equipment_area')->nullable();
            // $table->softDeletes();
            $table->timestamps();

            $table->foreign('dupa_per_project_id')->references('id')->on('dupa_per_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dupa_content_per_projects');
    }
};
