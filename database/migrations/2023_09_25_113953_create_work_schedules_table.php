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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('b3_project_id');
            $table->unsignedBigInteger('dupa_per_project_group_id');
            $table->timestamps();

            $table->foreign('b3_project_id')->references('id')->on('b3_projects');
            $table->foreign('dupa_per_project_group_id')->references('id')->on('dupa_per_project_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
