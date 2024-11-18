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
        Schema::create('pow_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_of_work_id');
            $table->unsignedBigInteger('sow_category_id');
            $table->timestamps();

            $table->foreign('program_of_work_id')->references('id')->on('program_of_works')->onDelete('cascade');
            $table->foreign('sow_category_id')->references('id')->on('sow_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pow_tables');
    }
};
