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
        Schema::create('b3_projects', function (Blueprint $table) {
            $table->id();
            $table->string('registry_no');
            $table->string('project_title');
            $table->unsignedBigInteger('project_nature_id');
            $table->unsignedBigInteger('project_nature_type_id');
            $table->string('concerned_division');
            $table->foreignId('barangay_id');
            $table->foreignId('district_id');
            $table->string('duration')->nullable();
            $table->string('contractor')->nullable();
            $table->string('location');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('project_nature_id')->references('id')->on('project_natures');
            $table->foreign('project_nature_type_id')->references('id')->on('project_nature_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('b3_projects');
    }
};
