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
                    'title' => 'required',
                    'description' => 'required',
                    't_date' => 'required'
                ];
            }else{
                return [
                    'title' => 'required',
                    'description' => 'required',
                    't_date' => 'required'
                ];
            }
        }

        public function messages(){
            return [
                'title.required' => 'Please enter title',
                'description.required' => 'Please enter instruction',
                't_date.required' => 'Please select date'
            ];
        }
    }
