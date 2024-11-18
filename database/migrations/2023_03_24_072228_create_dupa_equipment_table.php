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
        Schema::create('dupa_equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dupa_content_id');
            $table->unsignedBigInteger('equipment_id');
            $table->string('no_of_unit');
            $table->string('no_of_hour');
            // $table->softDeletes();
            $table->timestamps();

            $table->foreign('dupa_content_id')->references('id')->on('dupa_contents');
            $table->foreign('equipment_id')->references('id')->on('equipment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dupa_equipment');
    }
};
