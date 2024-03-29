<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalRequest extends FormRequest
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
            'title' => ['string'],
            'firstname' => ['required', 'string'],
            'midlename' => ['nullable', 'string'],
            'lastname' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'country' => ['required'],
            'affiliation' => ['required', 'string'],
            'another_affiliation' => ['required_if:affiliation,Another'],
            'email'     => ['email', 'required'],
            'secondemail' =>['nullable', 'email'],
            'phonenumber' => ['required', 'string'],
            'mobilenumber' => ['nullable', 'string']
        ];
    }
}
