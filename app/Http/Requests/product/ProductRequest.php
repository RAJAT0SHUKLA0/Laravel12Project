<?php

namespace App\Http\Requests\product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
//   public function rules(): array
// {
//     $rules = [
//         'name'           => 'required',
//         'category_id'    => 'required',
//         'sub_category_id'=> 'nullable',
//         'brand_id'       => 'nullable',
//         'description'    => 'nullable',
//         'image'          => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
//         'mrp'            => 'nullable',
//         'retailer_price' => 'nullable',
//         'hsn_code'       => 'nullable',
//         'gst'            => 'nullable',
//         'node_id'        => 'nullable|array',
//         'varient_id'     => 'required|array',
//     ];

//     // On POST → just check required & distinct inside array
//     if ($this->isMethod('post')) {
//         $rules['varient_id.*'] = 'required|distinct';
//     }

//     // On PUT → check each varient depending on node_id
//     if ($this->isMethod('put')) {
//         $varientIds = $this->input('varient_id', []);
//         $nodeIds    = $this->input('node_id', []);

//         foreach ($varientIds as $index => $varientId) {
//             $nodeId = $nodeIds[$index] ?? null;

//             if ($nodeId) {
//                 // existing row → only need to prevent request-level duplicates
//                 $rules["varient_id.$index"] = 'required|distinct';
//             } else {
//                 // new row → must be unique in DB too
//                 $rules["varient_id.$index"] = [
//                     'required',
//                     'distinct',
//                     Rule::unique('tbl_product_details', 'varient_id'),
//                 ];
//             }
//         }
//     }

//     return $rules;
// }

public function rules(): array
{
    $rules = [
        'name'           => 'required',
        'category_id'    => 'required',
        'sub_category_id'=> 'nullable',
        'brand_id'       => 'nullable',
        'description'    => 'nullable',
        'image'          => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        'mrp'            => 'nullable',
        'retailer_price' => 'nullable',
        'hsn_code'       => 'nullable',
        'gst'            => 'nullable',
        'node_id'        => 'nullable|array',
        'varient_id'     => 'required|array',
    ];

    if ($this->isMethod('post')) {
        $rules['varient_id.*'] = [
            'required',
            'distinct',
            Rule::unique('tbl_product_details', 'varient_id')
                ->where(fn ($q) => $q->where('product_id', $this->id)), // enforce uniqueness within same product
        ];
    }

    if ($this->isMethod('put')) {
        $varientIds = $this->input('varient_id', []);
        $nodeIds    = $this->input('node_id', []);

        foreach ($varientIds as $index => $varientId) {
            $nodeId = $nodeIds[$index] ?? null;

            if ($nodeId) {
                // existing row → skip DB unique, only prevent request duplicates
                $rules["varient_id.$index"] = 'required|distinct';
            } else {
                // new row → must be unique only inside this product
                $rules["varient_id.$index"] = [
                    'required',
                    'distinct',
                    Rule::unique('tbl_product_details', 'varient_id')
                        ->where(fn ($q) => $q->where('product_id', $this->id)),
                ];
            }
        }
    }

    return $rules;
}

    public function messages(): array
    {
        return [
            'name.required'             => 'Name is required.',
            'category_id.required'            => 'category_id is required.',
            'varient_id.required'            => 'varient_id is required.',
            'varient_id.distinct' => 'Each variant must be unique. Duplicate values are not allowed.',
            'image.mimes'         => 'Profile picture must be a file of type: jpg, jpeg, png, webp.',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
