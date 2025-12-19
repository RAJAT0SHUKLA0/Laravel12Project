<?php

namespace App\Http\Requests\seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class SellerRequest extends FormRequest
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
            'shop_name'        =>'required|string|max:255',
            'email'            => 'required|email',
            'mobile'            => 'required|numeric|digits:10',
            'whatsapp_no'       => 'required|numeric|digits:10',
           'profile_pic'      => $imageRoule.'file|mimes:jpg,jpeg,png,webp|max:2048',
            'addhar_front_pic' => $imageRoule.'file|mimes:jpg,jpeg,png,webp|max:2048',
            'addhar_back_pic'  => $imageRoule.'file|mimes:jpg,jpeg,png,webp|max:2048',
            'state_id'            => 'required',
            'city_id'             => 'required',
            'latitude'             => 'required',
            'longitude'             => 'required',
            'address'             => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Name is required.',
             'shop_name.required'             => 'ShopName is required.',
            'email.required'            => 'Email is required.',
            'email.email'               => 'Email must be a valid email address.',
            'email.unique'              => 'This email is already taken.',
            'phone.required'            => 'Phone number is required.',
            'phone.numeric'             => 'Phone must contain only numbers.',
            'phone.digits'              => 'Phone must be exactly 10 digits.',
            
            
            'phone.required'            => 'Phone number is required.',
            'phone.numeric'             => 'Phone must contain only numbers.',
            'phone.digits'              => 'Phone must be exactly 10 digits.',
           
           
            
            'profile_pic.required'      => 'Profile picture is required.',
            'profile_pic.mimes'         => 'Profile picture must be a file of type: jpg, jpeg, png, webp.',
            'profile_pic.max'           => 'Profile picture size should not exceed 2MB.',
            'addhar_front_pic.required' => 'Aadhar front picture is required.',
            'addhar_front_pic.mimes'    => 'Aadhar front picture must be jpg, jpeg, png, or webp.',
            'addhar_back_pic.required'  => 'Aadhar back picture is required.',
            'addhar_back_pic.mimes'     => 'Aadhar back picture must be jpg, jpeg, png, or webp.',
            
            'state.required'            => 'State is required.',
            'state.exists'              => 'Selected state is invalid.',
            'city.required'             => 'City is required.',
            'city.exists'               => 'Selected city is invalid.',
            'latitude.required'         => 'latitude is required.',
            'longitude.required'         => 'longitude is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
