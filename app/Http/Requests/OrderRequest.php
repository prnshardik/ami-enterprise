<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class OrderRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'name' => 'required'
                ];
            }else{
                return [
                    'name' => 'required'
                ];
            }
        }

        public function messages(){
            return [
                'name.required' => 'Please enter name',
                'order_date.required' => 'Please enter order date',
                'product_id.required' => 'Please select product',
                'product_id.min' => 'Please select product',
                'quantity.required' => 'Please enter quantity',
                'quantity.min' => 'Please enter quantity',
                'price.required' => 'Please enter price',
                'price.min' => 'Please select price',
            ];
        }
    }
