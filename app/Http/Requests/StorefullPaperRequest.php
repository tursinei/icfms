<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorefullPaperRequest extends FormRequest
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
            'abstract_id'   =>  ['required', 'integer'],
            'paper_file'    =>  ['required', 'file','mimes:pdf,doc,docx', 'max:5024'],
            'user_id'       =>  ['required', 'integer'],
        ];
    }

    public function messages()
    {
        return [
            'paper_file.max'    => 'Must not be greater than 5 Mb'
        ];
    }
}
