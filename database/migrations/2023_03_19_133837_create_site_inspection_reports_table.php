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
        Schema::create('site_inspection_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('communication_id');
            $table->string('sir_no');
            $table->string('project_title');
            $table->string('project_location');
            $table->text('findings', 10000);
            $table->text('recommendation', 10000);
            $table->string('status');

            $table->index('sir_no');
            $table->index('project_title');

            $table->foreign('communication_id')->references('id')->on('communications')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_inspection_reports');
    }
};
