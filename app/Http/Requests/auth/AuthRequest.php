<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\auth\ValidRole;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required','numeric','digits:10',new ValidRole],
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        return 
        [
            'username.required' => 'mobile is required.',
            'username.numeric'  => 'mobile must contain only numbers.',
            'username.digits'   => 'mobile must be exactly 10 digits.',
            'password.required' => 'Password is required.',
        ];
    }

     /**
     * Override failed validation behavior for debugging
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
        ->errorBag($this->errorBag)
        ->redirectTo($this->getRedirectUrl());
    }
}
