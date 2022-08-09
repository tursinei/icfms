<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
    public function rules()
    {
        return [
            'current' => ['required', 'current_password:web'],
            'password'  => ['required','confirmed'],
            'id'  => ['numeric']
        ];
    }

    public function messages()
    {
        return [
            'current.required' => 'The current password field is required',
            'current.current_password' => 'The current password field is incorrect',
        ];
    }
}
