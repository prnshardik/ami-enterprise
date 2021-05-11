<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class CustomerRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'party_name' => 'required|unique:customers,party_name,'.$this->id,
                    'billing_name' => 'required',
                    'contact_person' => 'required',
                    'mobile_number' => 'required',
                    'billing_address' => 'required',
                    'delivery_address' => 'required'
                ];
            }else{
                return [
                    'party_name' => 'required|unique:customers,party_name',
                    'billing_name' => 'required',
                    'contact_person' => 'required',
                    'mobile_number' => 'required',
                    'billing_address' => 'required',
                    'delivery_address' => 'required'
                ];
            }
        }

        public function messages(){
            return [
                'party_name.required' => 'Please enter party name',
                'party_name.unique' => 'Party name is already exists, please use another one',
                'billing_name.required' => 'Please enter billing name',
                'contact_person.required' => 'Please enter contact person',
                'mobile_number.required' => 'Please enter mobile number',
                'billing_address.required' => 'Please enter billing address',
                'delivery_address.required' => 'Please enter delivery address',
            ];
        }
    }
