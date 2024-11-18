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
        Schema::create('work_schedule_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_sched_id');
            $table->unsignedBigInteger('dupa_per_project_id');
            $table->string('duration')->nullable();
            $table->string('split_no');
            $table->timestamps();

            $table->foreign('work_sched_id')->references('id')->on('work_schedules')->onDelete('cascade');
            $table->foreign('dupa_per_project_id')->references('id')->on('dupa_per_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_items');
    }
};
