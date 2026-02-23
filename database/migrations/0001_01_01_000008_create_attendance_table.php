<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            $table->boolean('is_confirmed')->nullable()->default(1);
            $table->timestamp('confirmation_date')->useCurrent();

            $table->primary(['user_id', 'event_id']);

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('event_id')->references('event_id')->on('events');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
