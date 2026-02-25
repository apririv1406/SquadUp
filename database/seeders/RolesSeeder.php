<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->updateOrInsert(
            ['role_id' => 1],
            ['name' => 'admin']
        );

        DB::table('roles')->updateOrInsert(
            ['role_id' => 2],
            ['name' => 'organizer']
        );

        DB::table('roles')->updateOrInsert(
            ['role_id' => 3],
            ['name' => 'member']
        );
    }
}
