<?php

namespace App\Http\Requests\city;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;


class CityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
             'state_id'            => 'required',
            
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'City Name is required.',
            
            
            'state.required'            => 'State is required.',
            
            
            
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}