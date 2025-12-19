<?php

namespace App\Http\Requests\category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        
        $imageRoule = $this->isMethod('post')?'required|':'nullable|';
       


        return [
            'name'             => 'required|string|max:255',
             'description'             => 'required|string|max:255',
             'brand_id' => 'required|integer|exists:tbl_brands,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Name is required.',
            'description.required'       => 'description is required.',
             'brand_id.required'       => 'brand is required.',
            
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
