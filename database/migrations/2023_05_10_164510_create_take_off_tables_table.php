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
        Schema::create('take_off_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('take_off_id');
            $table->unsignedBigInteger('sow_category_id')->nullable();
            $table->unsignedBigInteger('table_dupa_component_formula_id');  // it was table_dupa_component_id before
            $table->string('contingency')->nullable();
            $table->string('table_say')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('take_off_id')->references('id')->on('take_offs')->onDelete('cascade');
            $table->foreign('sow_category_id')->references('id')->on('sow_categories');
            $table->foreign('table_dupa_component_formula_id')->references('id')->on('table_dupa_component_formulas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('take_off_tables');
    }
};
