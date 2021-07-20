<?php

    namespace Database\Seeders;
    use App\Models\Product;

    use Illuminate\Database\Seeder;

    class ProductSeeder extends Seeder{
        public function run(){
            for($i=1; $i<=5; $i++){
                Product::create([
                    'name' => "Product $i",
                    'quantity' => 0,
                    'code' => "PRODUCT_$i",
                    'unit' => 5 * $i,
                    'price' => 5 * $i,
                    'note' => "lorem ipsum $i",
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);
            }
        }
    }
