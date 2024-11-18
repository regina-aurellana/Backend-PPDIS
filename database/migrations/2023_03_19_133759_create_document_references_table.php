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
        Schema::create('document_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('communication_id');
            $table->string('reference_number');
            $table->string('document_source');

            $table->string('folder', 255)->nullable();
            $table->string('filename', 255);
            $table->string('original_filename', 255);
            $table->timestamps();

            $table->foreign('communication_id')->references('id')->on('communications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_references');
    }
};
