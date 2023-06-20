<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreinvoiceRequest extends FormRequest
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
            'invoice_id'    =>  ['nullable', 'integer'],
            'attribut'      =>  ['array'],
            'role'          =>  ['string','nullable'],
            'abstract_title'=>  ['nullable','string'],
            'invoice_number'=>  ['required'],
            'currency'      =>  ['required', 'string'],
            'nominal'       =>  ['required'],
            'user_id'       =>  ['required', 'integer'],
            'tgl_invoice'   =>  ['required', 'date']
        ];
    }
}
