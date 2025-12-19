<?php

namespace App\Http\Requests\bill;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class BillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'seller_id'          => 'required|exists:tbl_sellers,id',
            'cheque_clear_date'  => 'required|date',
            'amount'             => 'required|numeric|min:0',
            'image'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'seller_id.required' => 'Seller is required.',
            'date.required' => 'Cheque date is required.',
            'cheque_clear_date.required' => 'Clear date is required.',
            'amount.required' => 'Amount is required.',
            'image.image' => 'Image must be a valid image file (jpg, png).',
        ];
    }
    
     /**
     * Override failed validation behavior for debugging
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
        ->errorBag($this->errorBag)
        ->redirectTo($this->getRedirectUrl());
    }
}
