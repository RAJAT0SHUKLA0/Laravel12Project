<?php

namespace App\Http\Requests\settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;


class SubMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('post') || $this->isMethod('put')) {
            return [
                'name'    => 'required',
                'menu_id' => 'required',
                'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'parent_id' =>'nullable',
                'type' =>'required',
                'action'=>'nullable',
                'order'=>'nullable',

                'color_code'=>'required',

            ];
        }
        return [];
    }

    public function messages(): array
    {
          if ($this->isMethod('post')|| $this->isMethod('put')) {
            return [
                'name.required'             => ' Name is required.',
                 'menu_id.required'             => ' menu is required.',
                   'image.required'      => 'image is required.',
                'image.mimes'         => 'image must be a file of type: jpg, jpeg, png, webp.',
                'image.max'           => 'image size should not exceed 2MB.',

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