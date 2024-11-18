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
        Schema::create('labors', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->nullable();
            $table->string('designation');
            $table->string('hourly_rate');
            $table->boolean('active')->default(true)->nullable();
            $table->string('group')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('item_code');
            $table->index('designation');
            $table->index('active');
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('labors');
    }
};
