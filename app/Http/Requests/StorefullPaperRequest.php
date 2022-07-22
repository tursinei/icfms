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
            'paper_file'    =>  ['required', 'file','mimes:pdf,doc,docx'],
            'user_id'       =>  ['required', 'integer'],
        ];
    }
}
