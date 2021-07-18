<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class TaskRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'type' => 'required',
                    'users' => 'required',
                    't_date' => 'required'
                ];
            }else{
                return [
                    'type' => 'required',
                    'users' => 'required',
                    't_date' => 'required'
                ];
            }
        }

        public function messages(){
            return [
                'type.required' => 'Please select type',
                'users.required' => 'Please select atleast one user',
                't_date.required' => 'Please select date'
            ];
        }
    }
