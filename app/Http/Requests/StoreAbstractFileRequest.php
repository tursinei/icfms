<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbstractFileRequest extends FormRequest
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
            'presenter' => ['required', 'string'],
            'authors' => ['required', 'string'],
            'topic_id' => ['required', 'integer'],
            'presentation' => ['required', 'string'],
            'abstract_title' => ['required', 'string'],
            'paper_title' => ['required', 'string'],
            'abstract' => ['nullable','string'],
            'user_id' => ['required','integer'],
            'abstract_file' => ['required', 'file','mimes:pdf,doc,docx','max:3000'],
        ];
    }

    public function messages()
    {
        return [
            'abstract_file.max' => 'Must not be greater than 3 Mb'
        ];
    }
}
