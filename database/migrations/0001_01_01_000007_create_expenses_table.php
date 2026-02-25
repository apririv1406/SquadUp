<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements('expense_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('payer_id');
            $table->decimal('amount', 10, 2);
            $table->string('description', 255);
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->boolean('settled')->default(0);

            $table->foreign('event_id')->references('event_id')->on('events');
            $table->foreign('payer_id')->references('user_id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
