<?php

    namespace Database\Seeders;
    use App\Models\Product;

    use Illuminate\Database\Seeder;

    class ProductSeeder extends Seeder{
        public function run(){
            $colors = ['red', 'green', 'blue', 'yellow', 'brown'];
            
            for($i=1; $i<=5; $i++){
                $k = array_rand($colors);

                Product::create([
                    'name' => 'Product '.$i,
                    'quantity' => 5 * $i,
                    'unit' => 5 * $i,
                    'color' => $colors[$k],
                    'price' => 5 * $i,
                    'note' => 'lorem ipsum '.$i,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);
            }
        }
    }
