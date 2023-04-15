<?php

namespace App\Http\Requests\category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
{

    public function rules()
    {
        return [
            "title" => "required"
        ];
    }

    public function messages()
    {
        return [
            'title.required' => "Kategorya nomi kiritilmadi"
        ];
    }
}
