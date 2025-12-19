<?php

namespace App\Http\Requests\leaveType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;


class LeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
             
            
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'LeaveType is required.',
            
            
           
            
            
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}