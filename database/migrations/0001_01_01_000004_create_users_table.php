<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->unsignedInteger('role_id');
            $table->string('name', 100);
            $table->string('email', 100);
            $table->char('password', 60);
            $table->timestamp('created_at')->useCurrent();
            $table->string('google_id', 255)->nullable();

            $table->foreign('role_id')->references('role_id')->on('roles');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
