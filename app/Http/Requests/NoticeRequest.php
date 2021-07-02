<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class NoticeRequest extends FormRequest{
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
                'title.required' => 'Please enter title',
                'description.required' => 'Please enter description'
            ];
        }
    }
