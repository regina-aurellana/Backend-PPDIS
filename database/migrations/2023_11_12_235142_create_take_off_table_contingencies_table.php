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
        Schema::create('take_off_table_contingencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('take_off_table_id');
            $table->string('contingency');
            $table->timestamps();

            $table->foreign('take_off_table_id')->references('id')->on('take_off_tables')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('take_off_table_contingencies');
    }
};
