<?php

    namespace Database\Seeders;
    use App\Models\Order;
    use App\Models\OrderDetails;

    use Illuminate\Database\Seeder;

    class OrderSeeder extends Seeder{
        
        public function run(){
            for($i=1; $i<=3; $i++){
                $order_id = Order::insertGetId([
                    'name' => "Order $i",
                    'order_date' => date('Y-m-d'),
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);

                for($j=1; $j<=$i; $j++){
                    $order_detail_crud = [
                        'order_id' => $order_id,
                        'product_id' => $j,
                        'quantity' => $j * 5,
                        'price' => $j * 5,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => 1
                    ];

                    OrderDetails::insertGetId($order_detail_crud);
                }
            }
        }
    }
