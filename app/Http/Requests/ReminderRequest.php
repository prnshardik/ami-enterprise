<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class ReminderRequest extends FormRequest{
        
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'title' => 'required'
                ];
            }else{
                return [
                    'title' => 'required'
                ];
            }
        }

        public function messages(){
            return [
                'title.required' => 'Please enter title'
            ];
        }
    }
