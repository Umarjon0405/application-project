<?php

namespace App\Http\Requests\user;

use App\Http\Requests\MainRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends MainRequest
{

    public function rules()
    {
        return [
            "username" => "required|string",
            "full_name" => "required|string",
            "password" => "required|string|min:4"
        ];
    }

    public function messages()
    {
        return [
            "username.required" => "Foydalanuvchi nom kiritilmadi",
            "full_name.required" => "To'liq ism kiritilmadi",
            "password.required" => "Parol  kiritilmadi",
            "password.min" => "Parol kamida 4 ta belgidan iborat bo'lishi kerak",
        ];
    }
}
