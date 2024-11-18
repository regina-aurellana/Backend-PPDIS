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
        Schema::create('dupa_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dupa_id');
            $table->string('minor_tool_percentage')->nullable();
            $table->string('consumable_percentage')->nullable();
            $table->string('material_area')->nullable();
            $table->string('equipment_area')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('dupa_id')->references('id')->on('dupas');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dupa_contents');
    }
};
