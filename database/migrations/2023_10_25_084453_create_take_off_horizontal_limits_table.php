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
        Schema::create('take_off_horizontal_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('take_off_id');
            $table->string('limit');
            $table->string('length');
            $table->timestamps();

            $table->foreign('take_off_id')->references('id')->on('take_offs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('take_off_horizontal_limits');
    }
};
