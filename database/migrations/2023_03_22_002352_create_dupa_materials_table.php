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
        Schema::create('dupa_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dupa_content_id');
            $table->unsignedBigInteger('material_id');
            $table->string('quantity');
            // $table->softDeletes();
            $table->timestamps();

            $table->foreign('dupa_content_id')->references('id')->on('dupa_contents');
            $table->foreign('material_id')->references('id')->on('materials');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('dupa_materials');
    }
};
