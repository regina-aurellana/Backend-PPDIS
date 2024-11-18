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
        Schema::create('project_nature_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_nature_id');
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('project_nature_id')->references('id')->on('project_natures');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('project_nature_types');
    }
};
