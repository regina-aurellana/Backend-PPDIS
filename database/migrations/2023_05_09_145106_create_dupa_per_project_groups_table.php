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
        Schema::create('dupa_per_project_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('b3_project_id');
            $table->string('group_no');
            $table->string('name', 255)->nullable();
            $table->timestamps();

            $table->foreign('b3_project_id')->references('id')->on('b3_projects');

            $table->unique(['group_no', 'b3_project_id'], 'unique_group_no_per_project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dupa_per_project_groups');
    }
};
