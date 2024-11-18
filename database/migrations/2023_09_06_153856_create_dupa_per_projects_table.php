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
        Schema::create('dupa_per_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dupa_id');
            $table->unsignedBigInteger('b3_project_id');
            $table->unsignedBigInteger('sow_category_id');
            $table->unsignedBigInteger('subcategory_id');
            $table->unsignedBigInteger('dupa_per_project_group_id')->nullable();
            $table->string('item_number');
            $table->string('description');
            $table->unsignedBigInteger('unit_id');
            $table->string('output_per_hour');
            $table->unsignedBigInteger('category_dupa_id')->nullable();
            $table->string('direct_unit_cost')->nullable();
            // $table->softDeletes();
            $table->timestamps();

            $table->foreign('dupa_id')->references('id')->on('dupas');
            $table->foreign('b3_project_id')->references('id')->on('b3_projects');
            $table->foreign('sow_category_id')->references('id')->on('sow_categories');
            $table->foreign('subcategory_id')->references('id')->on('sow_sub_categories');
            $table->foreign('dupa_per_project_group_id')->references('id')->on('dupa_per_project_groups');
            $table->foreign('unit_id')->references('id')->on('unit_of_measurements');
            $table->foreign('category_dupa_id')->references('id')->on('category_dupas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dupa_per_projects');
    }
};
