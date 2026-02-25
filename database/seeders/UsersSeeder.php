<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->updateOrInsert([
            [
                'user_id'   => 1,
                'role_id'   => 1, // admin
                'name'      => 'Admin Demo',
                'email'     => 'admin@squadup.com',
                'password'  => Hash::make('admin123'),
                'google_id' => null,
                'created_at'=> now(),
            ],
            [
                'user_id'   => 2,
                'role_id'   => 3, // member
                'name'      => 'Miembro Demo',
                'email'     => 'member@squadup.com',
                'password'  => Hash::make('member123'),
                'google_id' => null,
                'created_at'=> now(),
            ],
        ]);
    }
}
