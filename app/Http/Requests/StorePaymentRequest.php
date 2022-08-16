<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'payment_id'    =>  ['required', 'integer'],
            // 'note'          =>  ['required_if:payment_id,==,0', 'file' ,'mimes:pdf,jpg,jpeg,png', 'max:5024'],
            'currency'      =>  ['required', 'string'],
            'nominal'       =>  ['required'],
            'user_id'       =>  ['required', 'integer'],
        ];
    }

    public function messages()
    {
        return [
            'note.required_if' => 'Payment Note Field is Required',
            'note.file'     => 'Payment Note Field Should not be empty',
            'note.max'      => 'Must not be greater than 5 Mb'
        ];
    }
}
