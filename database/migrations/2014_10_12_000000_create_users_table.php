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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('position');
            $table->boolean('is_active');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->foreignId('role_id')->constrained();
            $table->foreignId('team_id')->nullable()->constrained();
            $table->softDeletes();

            $table->index('role_id');
            $table->index('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
