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
        Schema::create('project_durations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_sched_id');
            $table->string('no_of_days');
            $table->timestamps();

            $table->foreign('work_sched_id')->references('id')->on('work_schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_durations');
    }
};
