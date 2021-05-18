<?php

    namespace Database\Seeders;
    use App\Models\Task;

    use Illuminate\Database\Seeder;

    class MyTaskSeeder extends Seeder{

        public function run(){
            for($i=1; $i<=5; $i++){
                Task::create([
                    'title' => "My Title $i",
                    'user_id' => $i,
                    'description' => "Description $i",
                    'target_date' => date('Y-m-d'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);
            }
        }
    }