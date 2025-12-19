<?php

namespace App\Http\Requests\varient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;


class VarientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
             'unit_id'            => 'required',
            
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'varient Name is required.',
            'unit.required'            => 'unit is required.',
            
            
            
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}