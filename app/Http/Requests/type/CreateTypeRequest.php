<?php

namespace App\Http\Requests\type;

use App\Http\Requests\MainRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateTypeRequest extends MainRequest
{

    public function rules()
    {
        return [
            "title" => 'required|string',
            'description' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => "Ariza turi uchun nom kiritilmadi",
            'description.required' => 'Ariza mazmuni kiritilmadi'
        ];
    }
}
