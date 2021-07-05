<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class ProductsRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'name' => 'required|unique:products,name,'.$this->id
                ];
            }else{
                return [
                    'name' => 'required|unique:products,name'
                ];
            }
        }

        public function messages(){
            return [
                'name.required' => 'Please enter name',
                'name.unique' => 'Prodcut name is already exists, please use another one',
                'quantity.required' => 'Please enter quantity',
                'unit.required' => 'Please enter unit',
                'color.required' => 'Please enter color',
                'price.required' => 'Please enter price'
            ];
        }
    }
