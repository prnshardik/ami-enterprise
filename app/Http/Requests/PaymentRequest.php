<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class PaymentRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            return [
                'file' => 'required|mimes:xls,xlsx,csv'
            ];
        }

        public function messages(){
            return [
                'file.required' => 'Please select file',
                'file.mimes' => 'Please select file with xls, xlsx or csv format'
            ];
        }
    }
