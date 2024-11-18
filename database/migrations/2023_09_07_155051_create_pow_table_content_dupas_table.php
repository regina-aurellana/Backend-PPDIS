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
        Schema::create('pow_table_content_dupas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pow_table_content_id');
            $table->unsignedBigInteger('dupa_per_project_id');
            $table->string('quantity');
            $table->string('total_estimated_direct_cost');
            $table->timestamps();

            $table->foreign('pow_table_content_id')->references('id')->on('pow_table_contents')->onDelete('cascade');
            $table->foreign('dupa_per_project_id')->references('id')->on('dupa_per_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pow_table_content_dupas');
    }
};
