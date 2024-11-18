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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_sched_item_id');
            $table->string('week_no');
            $table->string('day_no');
            $table->string('duration_no');
            $table->string('group_no');
            $table->timestamps();

            $table->foreign('work_sched_item_id')->references('id')->on('work_schedule_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
