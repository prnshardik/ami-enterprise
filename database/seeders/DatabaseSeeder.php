<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder{
    public function run(){
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            CustomerSeeder::class,
            TaskSeeder::class,
            MyTaskSeeder::class,
            NoticeSeeder::class,
        ]);
    }
}
