<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dupas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subcategory_id');
            $table->string('item_number');
            $table->string('description');
            $table->unsignedBigInteger('unit_id');
            $table->string('output_per_hour');
            $table->unsignedBigInteger('category_dupa_id')->nullable();
            $table->string('direct_unit_cost')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('subcategory_id')->references('id')->on('sow_sub_categories');
            $table->foreign('unit_id')->references('id')->on('unit_of_measurements');
            $table->foreign('category_dupa_id')->references('id')->on('category_dupas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dupas');
    }
};
