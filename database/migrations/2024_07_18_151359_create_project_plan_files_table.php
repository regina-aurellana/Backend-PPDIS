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
        Schema::create('project_plan_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_plan_id');
            $table->string('filename');
            $table->timestamps();
            
            $table->foreign('project_plan_id')->references('id')->on('project_plans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_plan_files');
    }
};
