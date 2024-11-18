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
        Schema::create('a_b_c_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('abc_id');
            $table->string('total_cost')->nullable();
            $table->timestamps();

            $table->foreign('abc_id')->references('id')->on('a_b_c_s')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_b_c_contents');
    }
};
