<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class PaymentReminderRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            return [
                'party_name' => 'required',
                'next_date' => 'required',
                'next_time' => 'required'
            ];
        }

        public function messages(){
            return [
                'party_name.required' => 'Please select party name',
                'next_date.required' => 'Please select next date',
                'next_time.required' => 'Please select next time'
            ];
        }
    }
