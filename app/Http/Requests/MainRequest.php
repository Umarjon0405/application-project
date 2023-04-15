<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class MainRequest extends FormRequest
{

    public function authorize():bool
    {
        return true;
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => env("MESSAGE_VALIDATION"),
            'first' => $validator->errors()->first(),
            'error' => $validator->errors()
        ]));
    }
}
