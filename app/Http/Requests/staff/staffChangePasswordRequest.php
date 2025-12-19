<?php

namespace App\Http\Requests\staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class staffChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'password'         => 'required',
             'confirm_password' =>'required|same:password'
        ];
    }
    
    public function messages(): array
    {
        return [
            'password.required'             => 'password is required.',
            'confirm_password.required'     => 'confirm_password is required.',
            'confirm_password.same'         => 'confirm_password is miss match .',
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
