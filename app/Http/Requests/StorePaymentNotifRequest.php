<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentNotifRequest extends FormRequest
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
            'paymentnotif_id'   =>  ['nullable', 'integer'],
            'attribut'          =>  ['array'],
            'currency'      =>  ['required', 'string'],
            'nominal'       =>  ['required'],
            'user_id'       =>  ['required', 'integer'],
            'tgl_invoice'   =>  ['required', 'date']
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
