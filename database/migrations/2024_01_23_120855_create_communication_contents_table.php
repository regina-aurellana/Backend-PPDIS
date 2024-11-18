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
        Schema::create('communication_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('communication_id');
            $table->unsignedBigInteger('routed_to_user_id');
            $table->unsignedBigInteger('routed_by_user_id');
            $table->string('remarks')->nullable();
            $table->string('action_taken')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('communication_id');
            $table->index('routed_to_user_id');
            $table->index('routed_by_user_id');

            $table->foreign('communication_id')->references('id')->on('communications')->onDelete('cascade');
            $table->foreign('routed_to_user_id')->references('id')->on('users');
            $table->foreign('routed_by_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_contents');
    }
};
