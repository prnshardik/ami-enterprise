<?php

    namespace Database\Seeders;
    use App\Models\Customer;

    use Illuminate\Database\Seeder;

    class CustomerSeeder extends Seeder{

        public function run(){
            for($i=1; $i<=5; $i++){
                Customer::create([
                    'party_name' => "Party Name $i",
                    'billing_name' => "Billing Name $i",
                    'contact_person' => "Contact Person $i",
                    'mobile_number' => "987987989$i",
                    'billing_address' => "Billing Address $i",
                    'delivery_address' => "Delivery Address $i",
                    'electrician' => "Electrician $i",
                    'electrician_number' => "987987988$i",
                    'architect' => "Architect $i",
                    'architect_number' => "987987987$i",
                    'office_contact_person' => "Office Contact Person $i",
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);
            }
        }
    }
