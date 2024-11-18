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
        Schema::create('sow_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_sow_sub_category_id')->nullable();
            $table->unsignedBigInteger('sow_sub_category_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('parent_sow_sub_category_id')->references('id')->on('sow_sub_categories');
            $table->foreign('sow_sub_category_id')->references('id')->on('sow_sub_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sow_references');
    }
};
