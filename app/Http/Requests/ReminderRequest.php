<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReminderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
            'note.required' => 'Please enter description'
        ];
    }
}
