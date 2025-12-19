<?php

namespace App\Http\Requests\brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [
            'name'             => 'required|string|max:255',
              'image'      => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
              ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Name is required.',
             'image.mimes'         => 'Profile picture must be a file of type: jpg, jpeg, png, webp.',
            
            
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
