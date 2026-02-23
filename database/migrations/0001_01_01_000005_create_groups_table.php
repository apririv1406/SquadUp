<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('group_id');
            $table->unsignedBigInteger('organizer_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('invitation_code', 10)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('organizer_id')->references('user_id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
