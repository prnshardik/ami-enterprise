<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class MyTaskRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'type' => 'required',
                    'description' => 'required',
                    't_date' => 'required'
                ];
            }else{
                return [
                    'type' => 'required',
                    'description' => 'required',
                    't_date' => 'required'
                ];
            }
        }

        public function messages(){
            return [
                'type.required' => 'Please select type',
                'description.required' => 'Please enter instruction',
                't_date.required' => 'Please select date'
            ];
        }
    }
