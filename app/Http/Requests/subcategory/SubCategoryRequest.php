<?php

namespace App\Http\Requests\subcategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class SubCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [
            'name'             => 'required|string|max:255',
             'description'             => 'required|string|max:255',
             'category_id'             => 'required',
            
            
            
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Name is required.',
            'description.required'       => 'description is required.',
            'categroy_id.required'             => 'category is required.',
            'categroy_id.exists'               => 'Selected category is invalid.',
            
            
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
