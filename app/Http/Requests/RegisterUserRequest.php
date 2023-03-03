<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'firstname' => ['required', 'string', 'max:255'],
            'midlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'email:rfc,dns', 'max:255', 'unique:users'],
            'secondemail' => ['bail', 'nullable', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'confirmed', 'min:6'],
            'affiliation' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'phonenumber' => ['required', 'string', 'max:255'],
            'mobilenumber' => ['required', 'string', 'max:255'],
            'presentation' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return [
            'presentation' => 'Your Role'
        ];
    }
}
