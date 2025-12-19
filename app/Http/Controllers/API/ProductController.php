<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Message;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use App\Models\Product;
use App\Models\Varient;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function ProductList()
    {
        
        try 
        {
            $data = Product::with([
                'getdetail.varient.unit',
                'category' => function ($query) {
                    $query->select('id', 'name');
                },
            ])
            ->orderBy('id', 'desc')
            ->where('status','!=',3)
            ->get()
            ->map(function ($product) {
                return [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'price'       => $product->price,
                    'hsn_code'    => $product->hsn_code,
                    'status'      => $product->status == 1 ? 'Active' : 'In Active',
                    'description' => $product->description,
                    'category'    => $product->category->name ?? null,
                    'image'       => !empty($product->image)
                                        ? asset('storage/uploads/product/' . $product->image)
                                        : '',
                    'varient'     => $product->getdetail->map(function ($detail) {
                        return [
                            'id'   => $detail->id,
                            'price'   => $detail->mrp,
                            'name' => $detail->varient
                                ? $detail->varient->name . ' ' . ($detail->varient->unit->name ?? '')
                                : null,
                        ];
                    }),
                ];
            });


            if ($data) {
                
                    ApiLogService::success(sprintf(Message::PRODUCT_LIST,'PRODUCT'), $data);
                    return ApiResponseService::success(sprintf(Message::PRODUCT_LIST,'PRODUCT'), $data);  
                
            }else{
                    ApiLogService::success(sprintf(Message::PRODUCT_LIST_NOT_FOUND ,'PRODUCT'), []);
                    return ApiResponseService::success(sprintf(Message::PTODUCT_LIST_NOT_FOUND,'PRODUCT'), []);
                }
            
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    public function addProduct(Request $request)
    {
        try {
            ApiLogService::info('product request received', $request->all());
            $validator = Validator::make($request->all(), [
                'name'            =>     'required',
                'category_id'          =>      'required',
                'varient_id'            =>     'required',
                'price'           =>       'required',
                'hsn_code'            =>        'required',

               


            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }

            $Product  = new Product;
            $Product->name =$request->name;
            $Product->category_id = $request->category_id;
            $Product->sub_category_id = $request->sub_category_id;
            $Product->varient_id = $request->varient_id;
            $Product->hsn_code = $request->hsn_code;
            $Product->description = $request->description;
            $Product->price = $request->price;
            $Product->status = 1;
            if($Product->save()){
                ApiLogService::success(sprintf(Message::PRODUCT_SUCCESS,'add'), []);
                return ApiResponseService::success(sprintf(Message::PRODUCT_SUCCESS,'add'), []);  
            }else{
                ApiLogService::success(sprintf(Message::PTODUCT_UNSUCCESS,'add'), []);
                return ApiResponseService::success(sprintf(Message::PTODUCT_UNSUCCESS,'add'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    public function updateProduct(Request $request)
    {
        try {
            ApiLogService::info('staff request received', $request->all());
            $validator = Validator::make($request->all(), [
                'name'            =>     'required',
                'category_id'          =>      'required',
                'varient_id'            =>     'required',
                'price'           =>       'required',
                'hsn_code'            =>        'required',

               


            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }

            $Product  =  Product::where('id',$request->product_id)->first();
            $Product->name =$request->name;
            $Product->category_id = $request->category_id;
            $Product->sub_category_id = $request->sub_category_id;
            $Product->varient_id = $request->varient_id;
            $Product->hsn_code = $request->hsn_code;
            $Product->description = $request->description;
            $Product->price = $request->price;
            if($Product->save()){
                ApiLogService::success(sprintf(Message::PRODUCT_SUCCESS,'update'), []);
                return ApiResponseService::success(sprintf(Message::PRODUCT_SUCCESS,'update'), []);  
            }else{
                ApiLogService::success(sprintf(Message::PTODUCT_UNSUCCESS,'update'), []);
                return ApiResponseService::success(sprintf(Message::PTODUCT_UNSUCCESS,'update'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    
    public function updateProductPrice(Request $request)
    {
        try {
            ApiLogService::info('staff request received', $request->all());
            $validator = Validator::make($request->all(), [
                'seller_id'             =>       'required|integer',
                'product_id'            =>       'required|integer',
                'varient_id'            =>       'required|integer',
                'price'                 =>       'required',
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }

            $Product  =  Product::where('id',$request->product_id)->first();
            $Product->name =$request->name;
            $Product->category_id = $request->category_id;
            $Product->sub_category_id = $request->sub_category_id;
            $Product->varient_id = $request->varient_id;
            $Product->hsn_code = $request->hsn_code;
            $Product->description = $request->description;
            $Product->price = $request->price;
            if($Product->save()){
                ApiLogService::success(sprintf(Message::PRODUCT_SUCCESS,'update'), []);
                return ApiResponseService::success(sprintf(Message::PRODUCT_SUCCESS,'update'), []);  
            }else{
                ApiLogService::success(sprintf(Message::PTODUCT_UNSUCCESS,'update'), []);
                return ApiResponseService::success(sprintf(Message::PTODUCT_UNSUCCESS,'update'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    
    
    public function ProductStatusUpdate(Request $request)
    {
        try 
        {
            ApiLogService::info('product request received', $request->all());
            $user = Product::where('id', $request->product_id)->first();
            if ($user) {
                $user->status = $request->status;
                if($user->save()){
                    ApiLogService::success(Message::PTODUCT_STATUS_SUCCESS, []);
                    return ApiResponseService::success(Message::PTODUCT_STATUS_SUCCESS, []);  
                }else{
                    ApiLogService::success(Message::PTODUCT_STATUS_UNSUCCESS, []);
                    return ApiResponseService::success(Message::PTODUCT_STATUS_UNSUCCESS, []);
                }
            }
            
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
}
