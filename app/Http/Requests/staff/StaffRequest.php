<?php

namespace App\Http\Requests\staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Models\User;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $passwordRule = $this->isMethod('post')?'required|min:6':'nullable';
        $imageRoule = $this->isMethod('post')?'required|':'nullable|';
        $mobileRule = ['required', 'numeric', 'digits:10'];
        if ($this->isMethod('post') || ($this->mobile && $this->role_id)) {
           $existUser = User::where('mobile',$this->mobile)->where('role_id', $this->role_id)->first();
           if($existUser){
            $uniqueMobile = Rule::unique('tbl_users', 'mobile')
                ->where('role_id', $this->role_id);
            $mobileRule[] = $uniqueMobile;
               
           }
        }
        return [
            'name'             => 'required|string|max:255',
            'email'            => 'required|email',
            'mobile'            => $mobileRule,
            'password'         => $passwordRule,
            'joining_date'     => 'required|date',
            'profile_pic'      => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'addhar_front_pic' => $imageRoule.'file|mimes:jpg,jpeg,png,webp|max:2048',
            'addhar_back_pic'  => $imageRoule.'file|mimes:jpg,jpeg,png,webp|max:2048',
            'role_id'             => 'required',
            'state_id'            => 'required',
            'city_id'             => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Name is required.',
            'email.required'            => 'Email is required.',
            'email.email'               => 'Email must be a valid email address.',
            'mobile.unique'              => 'This mobile is already taken.',
            'mobile.required'            => 'Phone number is required.',
            'mobile.numeric'             => 'Phone must contain only numbers.',
            'mobile.digits'              => 'Phone must be exactly 10 digits.',
            'password.required'         => 'Password is required.',
            'password.min'              => 'Password must be at least 6 characters.',
            'joining_date.required'     => 'Joining date is required.',
            'joining_date.date'         => 'Joining date must be a valid date.',
            'profile_pic.required'      => 'Profile picture is required.',
            'profile_pic.mimes'         => 'Profile picture must be a file of type: jpg, jpeg, png, webp.',
            'profile_pic.max'           => 'Profile picture size should not exceed 2MB.',
            'addhar_front_pic.required' => 'Aadhar front picture is required.',
            'addhar_front_pic.mimes'    => 'Aadhar front picture must be jpg, jpeg, png, or webp.',
            'addhar_back_pic.required'  => 'Aadhar back picture is required.',
            'addhar_back_pic.mimes'     => 'Aadhar back picture must be jpg, jpeg, png, or webp.',
            'role.required'             => 'Role is required.',
            'role.exists'               => 'Selected role is invalid.',
            'state.required'            => 'State is required.',
            'state.exists'              => 'Selected state is invalid.',
            'city.required'             => 'City is required.',
            'city.exists'               => 'Selected city is invalid.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
