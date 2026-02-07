<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('role')->updateOrInsert(
            ['id_role' => 1],
            [
                'nama_role' => 'Super Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
