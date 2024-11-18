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
        Schema::create('take_off_table_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('take_off_table_id');
            $table->unsignedBigInteger('measurement_id');
            $table->timestamps();

            $table->foreign('take_off_table_id')->references('id')->on('take_off_tables')->onDelete('cascade');
            $table->foreign('measurement_id')->references('id')->on('unit_of_measurements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('take_off_table_fields');
    }
};
