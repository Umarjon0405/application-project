<?php

namespace App\Http\Requests\user;

use App\Http\Requests\MainRequest;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends MainRequest
{

    public function rules()
    {
        return [
            "username" => "required",
            "password" => "required"
        ];
    }

    public function messages()
    {
        return [
            "username.required" => "Foydalanuvchi nomi kiritilmadi",
            "password.required" => "Parol kiritilmadi"
        ];
    }
}

