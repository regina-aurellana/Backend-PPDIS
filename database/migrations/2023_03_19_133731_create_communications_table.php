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
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->string('comms_ref_no')->unique();
            $table->unsignedBigInteger('communication_category_id');
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('barangay_id')->nullable();
            $table->string('subject');
            $table->string('location')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();

            $table->index('comms_ref_no');

            $table->foreign('communication_category_id')->references('id')->on('communication_categories');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('barangay_id')->references('id')->on('barangays');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
