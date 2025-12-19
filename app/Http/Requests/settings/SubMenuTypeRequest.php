<?php

namespace App\Http\Requests\settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;


class SubMenuTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('post') || $this->isMethod('put')) {
            return [
                'name'             => 'required|string|max:255',
                 
                
            ];
        }else{
              return [];
          }
    }

    public function messages(): array
    {
          if ($this->isMethod('post')|| $this->isMethod('put')) {
            return [
                'name.required'             => 'State Name is required.',
                
                
               
                
                
            ];
          }else{
              return [];
          }
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}