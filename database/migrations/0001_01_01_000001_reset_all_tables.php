<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('attendance');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('events');
        Schema::dropIfExists('user_group');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        //
    }
};
