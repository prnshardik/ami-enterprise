<?php

namespace Database\Seeders;
use App\Models\User;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Super Admin',
            'phone' => '1234567890',
            'email' => 'superadmin@mail.com',
            'password' => bcrypt('Admin@123'),
            'is_admin' => 'y',
            'status' => 'active'
        ]);
    }
}
