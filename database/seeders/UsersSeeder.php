<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@squadup.com'],
            [
                'user_id'   => 1,
                'role_id'   => 1,
                'name'      => 'Admin Demo',
                'password'  => Hash::make('admin123'),
                'google_id' => null,
                'created_at'=> now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'member@squadup.com'],
            [
                'user_id'   => 2,
                'role_id'   => 3,
                'name'      => 'Miembro Demo',
                'password'  => Hash::make('member123'),
                'google_id' => null,
                'created_at'=> now(),
            ]
        );
    }
}
