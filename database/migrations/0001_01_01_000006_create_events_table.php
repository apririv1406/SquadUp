<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('event_id');
            $table->unsignedBigInteger('group_id');
            $table->string('title', 150);
            $table->string('location', 255)->nullable();
            $table->dateTime('event_date');
            $table->boolean('is_public')->default(0);
            $table->integer('capacity')->default(0);
            $table->string('sport_name', 255);
            $table->integer('creator_id');

            $table->foreign('group_id')->references('group_id')->on('groups');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
