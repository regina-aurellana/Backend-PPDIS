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
        Schema::create('b1_project_identifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_inspection_report_id');
            $table->unsignedBigInteger('communication_id');
            $table->string('b1_id_no')->nullable();
            $table->string('project_identification_no');
            $table->string('initial_project_name');
            $table->string('address');
            $table->string('requesting_party');
            $table->unsignedBigInteger('project_nature_id');
            $table->unsignedBigInteger('project_nature_type_id');
            $table->text('reason', 10000);
            $table->string('existing_condition');
            $table->string('estimated_beneficiary');
            $table->text('recommendation', 10000);
            $table->string('contact_no');
            $table->string('status');

            $table->index('b1_id_no');
            $table->index('site_inspection_report_id', 'sir_id');
            $table->index('communication_id', 'comm_id');

            $table->foreign('communication_id')->references('id')->on('communications')->onDelete('cascade');
            $table->foreign('site_inspection_report_id')->references('id')->on('site_inspection_reports')->onDelete('cascade');
            $table->foreign('project_nature_id')->references('id')->on('project_natures');
            $table->foreign('project_nature_type_id')->references('id')->on('project_nature_types');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b1_project_identifications');
    }
};
