<?php

    namespace Database\Seeders;
    use App\Models\Notice;

    use Illuminate\Database\Seeder;

    class NoticeSeeder extends Seeder{

        public function run(){
            for($i=1; $i<=5; $i++){
                Notice::create([
                    'title' => "Title $i",
                    'description' => "Description $i",
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);
            }
        }
    }
