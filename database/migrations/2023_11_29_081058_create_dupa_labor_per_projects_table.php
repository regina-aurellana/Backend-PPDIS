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
        Schema::create('dupa_labor_per_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dupa_content_per_project_id');
            $table->unsignedBigInteger('labor_id');
            $table->string('no_of_person');
            $table->string('no_of_hour');
            $table->string('group')->nullable();
            $table->string('final_price')->nullable();
            $table->timestamps();

            $table->foreign('dupa_content_per_project_id')->references('id')->on('dupa_content_per_projects');
            $table->foreign('labor_id')->references('id')->on('labors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dupa_labor_per_projects');
    }
};
