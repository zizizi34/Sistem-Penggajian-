<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email_user' => 'superadmin@gmail.com'],
            [
                'password_user' => Hash::make('12345678'),
                'id_role' => 1,
            ]
        );
    }
}
