<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Seller;
use App\Helper\Message;
use App\Utils\Uploads;
use Illuminate\Support\Facades\Hash;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use App\Utils\Crypto;

class SellerController extends Controller
{
    public function AddSeller(Request $request)
    {
        try {
            ApiLogService::info('staff request received', $request->all());
            $validator = Validator::make($request->all(), [
                'name'            =>     'required',
                'mobile'          =>      'required|unique:tbl_users,mobile',
                'email'            =>     'required',
                'state'           =>       'required',
                'city'            =>        'required',
                'profile_pic'     =>          'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
                'aadhar_front_pic' =>          'required|file|mimes:jpg,jpeg,png,webp|max:2048',
                'aadhar_back_pic'  =>      'required|file|mimes:jpg,jpeg,png,webp|max:2048',
               


            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }
             $staff_id ='';
            $user = Seller::orderby('id','desc')->whereNotNull('seller_id')->first();

            if (!empty($user)) {
                $lastStaffId = ltrim($user->seller_id, '0');
                $exploadData=explode('-',$lastStaffId);
                $lastStaffId = is_string($lastStaffId) ? (int)$exploadData[1] : 0;
                $nextStaffId = str_pad($lastStaffId + 1, 3, '0', STR_PAD_LEFT);
                $staff_id = $exploadData[0].'-'.$nextStaffId;
            }
            $User  = new Seller;
            $User->name =$request->name;
            $User->mobile = $request->mobile;
            $User->email = $request->email;
            $User->state_id = $request->state;
            $User->city_id = $request->city;
            $User->seller_id =$staff_id;
            $User->beat_id =$request->beat_id;
            $User->latitude =$request->latitude;
            $User->longitude =$request->longitude;
            $User->address =$request->address;
            $User->shop_name =$request->shop_name;
            $User->whatsapp_no =$request->whatsapp_no;
            $User->address =$request->address;
            $User->sellertype_id  = $request->seller_type;

            $User->password   =Hash::make( $request->mobile);
            if ($request->hasFile('profile_pic')) {
                $path = Uploads::uploadImage($request->file('profile_pic'),'profile','profile');
                $User->profile_pic = $path; 
            }
            if ($request->hasFile('aadhar_front_pic')) {
                $path =Uploads::uploadImage($request->file('aadhar_front_pic'),'aadhar','aadhar_front');
                $User->addhar_front_pic = $path;
            }
            if ($request->hasFile('aadhar_back_pic')) {
                $path = Uploads::uploadImage($request->file('aadhar_back_pic'),'aadhar','aadhar_back');
                $User->addhar_back_pic = $path;
            }
            if($User->save()){
                ApiLogService::success(sprintf(Message::STAFF_SUCCESS,'add'), []);
                return ApiResponseService::success(sprintf(Message::STAFF_SUCCESS,'add'), []);  
            }else{
                ApiLogService::success(sprintf(Message::STAFF_UNSUCCESS,'add'), []);
                return ApiResponseService::success(sprintf(Message::STAFF_UNSUCCESS,'add'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    public function SellerList()
    {
        try {
           $statusLabels = [
                0 => 'Inactive',
                1 => 'Active',
                2 => 'Deleted',
            ];
            $user = Seller::with(['state','city','area'])->orderby('updated_at','desc')->get()->map(function ($users) use ($statusLabels) {
                $users->status = $statusLabels[$users->status] ?? 'Unknown';
                $users->role_id = $users->role->name ?? 'Unknown';
                $users->profile_pic      =      !empty($users->profile_pic)?asset('storage/uploads/profile/'.$users->profile_pic):asset('profile/ic_profile_fall_back.jpg');
                $users->addhar_front_pic =      !empty($users->addhar_front_pic)?asset('storage/uploads/aadhar/'.$users->addhar_front_pic):'';
                $users->addhar_back_pic  =      !empty($users->addhar_back_pic)?asset('storage/uploads/aadhar/'.$users->addhar_back_pic):'';
                $users->city_id  =         !empty($users->city->name)?$users->city->name:'';
                $users->state_id  =      !empty($users->state->name)?$users->state->name:'';
                 $users->beat_id  =      !empty($users->area->name)?$users->area->name:'';
                unset($users->state);
                unset($users->city);
                unset($users->area);


                return $users;
            });
            
            if($user){
                ApiLogService::success(Message::STAFF_SUCCESS, $user);
                return ApiResponseService::success(Message::STAFF_LIST_SUCCESS, $user);  
            }else{
                ApiLogService::success(Message::STAFF_LIST_UNSUCCESS, []);
                return ApiResponseService::success(Message::STAFF_LIST_UNSUCCESS, []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    public function SellerDetail(Request $request)
    {
        try {
        $request->validate([
            'seller_id' => 'required|integer|exists:tbl_sellers,id'
        ]);
           ApiLogService::info('staff request received', $request->all());
           $statusLabels = [
                0 => 'Inactive',
                1 => 'Active',
                2 => 'Deleted',
            ];
            $user = Seller::findOrFail($request->seller_id);
            $user->status = $statusLabels[$user->status] ?? 'Unknown';
            $user->role_id = $user->role->name ?? 'Unknown';
            $user->city_id  =         !empty($user->city->name)?$user->city->name:'';
            $user->state_id  =      !empty($user->state->name)?$user->state->name:'';
            $user->beat_id  =      !empty($user->area->name)?$user->area->name:'';
             $user->profile_pic = !empty($users->profile_pic)?asset('storage/uploads/profile/'.$user->profile_pic):asset('profile/ic_profile_fall_back.jpg');
                $user->addhar_front_pic =  !empty($users->addhar_front_pic)?asset('storage/uploads/aadhar/'.$user->addhar_front_pic):'';
                $user->addhar_back_pic = !empty($users->addhar_back_pic)?asset('storage/uploads/aadhar/'.$user->addhar_back_pic):'';
                  unset($user->state);
                unset($user->city);
            unset($user->area);
            if($user){
                ApiLogService::success(Message::STAFF_SUCCESS, $user);
                return ApiResponseService::success(Message::STAFF_LIST_SUCCESS, $user);  
            }else{
                ApiLogService::success(Message::STAFF_LIST_UNSUCCESS, []);
                return ApiResponseService::success(Message::STAFF_LIST_UNSUCCESS, []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    public function UpdateSeller(Request $request)
    {
        try {
            ApiLogService::info('staff request received', $request->all());
            $validator = Validator::make($request->all(), [
                'name'            =>     'required',
                'email'            =>     'required',
                'state'           =>       'required',
                'city'            =>        'required',
                'profile_pic'     =>          'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
                'aadhar_front_pic' =>          'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
                'aadhar_back_pic'  =>      'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
                'seller_id'       =>      'required|integer|exists:tbl_sellers,id'


            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }
            $User = Seller::findOrFail($request->seller_id)->orderby('id','desc');
            $User->name =$request->name;
            $User->email = $request->email;
            $User->state_id = $request->state;
            $User->city_id = $request->city;
             $User->beat_id =$request->beat_id;
            $User->latitude =$request->latitude;
            $User->longitude =$request->longitude;
            $User->address =$request->address;
            $User->shop_name =$request->shop_name;
            $User->whatsapp_no =$request->whatsapp_no;
            $User->sellertype_id  = $request->seller_type;

            if ($request->hasFile('profile_pic')) {
                $path = Uploads::uploadImage($request->file('profile_pic'),'profile','profile');
                $User->profile_pic = $path; 
            }
            if ($request->hasFile('aadhar_front_pic')) {
                $path =Uploads::uploadImage($request->file('aadhar_front_pic'),'aadhar','aadhar_front');
                $User->addhar_front_pic = $path;
            }
            if ($request->hasFile('aadhar_back_pic')) {
                $path = Uploads::uploadImage($request->file('aadhar_back_pic'),'aadhar','aadhar_back');
                $User->addhar_back_pic = $path;
            }
            if($User->save()){
                ApiLogService::success(sprintf(Message::STAFF_SUCCESS,'update'), []);
                return ApiResponseService::success(sprintf(Message::STAFF_SUCCESS,'update'), []);  
            }else{
                ApiLogService::success(sprintf(Message::STAFF_UNSUCCESS,'update'), []);
                return ApiResponseService::success(sprintf(Message::STAFF_UNSUCCESS,'update'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    public function SellerStatusUpdate(Request $request)
    {
        try 
        {
            $request->validate([
            'seller_id' => 'required|integer|exists:tbl_sellers,id'
           ]);
            ApiLogService::info('staff request received', $request->all());
            $user = Seller::findOrFail($request->seller_id);
            if ($user) {
                $user->status = $request->status;
                if($user->save()){
                    ApiLogService::success(Message::STAFF_STATUS_SUCCESS, []);
                    return ApiResponseService::success(Message::STAFF_STATUS_SUCCESS, []);  
                }else{
                    ApiLogService::success(Message::STAFF_STATUS_UNSUCCESS, []);
                    return ApiResponseService::success(Message::STAFF_STATUS_UNSUCCESS, []);
                }
            }
            
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    
    public function SellerListBeatWise(Request $request)
    {
        try {
           $statusLabels = [
                0 => 'Inactive',
                1 => 'Active',
                2 => 'Deleted',
            ];
            $user = Seller::where('beat_id',$request->beat_id)->with(['state','city','area'])->orderby('updated_at','desc')->get()->map(function ($users) use ($statusLabels) {
                $users->status = $statusLabels[$users->status] ?? 'Unknown';
                $users->role_id = $users->role->name ?? 'Unknown';
                $users->profile_pic      =      !empty($users->profile_pic)?asset('storage/uploads/profile/'.$users->profile_pic):asset('profile/ic_profile_fall_back.jpg');
                $users->addhar_front_pic =      !empty($users->addhar_front_pic)?asset('storage/uploads/aadhar/'.$users->addhar_front_pic):'';
                $users->addhar_back_pic  =      !empty($users->addhar_back_pic)?asset('storage/uploads/aadhar/'.$users->addhar_back_pic):'';
                $users->city_id  =         !empty($users->city->name)?$users->city->name:'';
                $users->state_id  =      !empty($users->state->name)?$users->state->name:'';
                 $users->beat_id  =      !empty($users->area->name)?$users->area->name:'';
                unset($users->state);
                unset($users->city);
                unset($users->area);


                return $users;
            });
            
            if($user){
                ApiLogService::success(Message::STAFF_SUCCESS, $user);
                return ApiResponseService::success(Message::STAFF_LIST_SUCCESS, $user);  
            }else{
                ApiLogService::success(Message::STAFF_LIST_UNSUCCESS, []);
                return ApiResponseService::success(Message::STAFF_LIST_UNSUCCESS, []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
public function SellerProfile(Request $request)
{
    try {
        $request->validate([
            'seller_id' => 'required|integer|exists:tbl_sellers,id'
        ]);

        // Label Mappings
        $statusLabels = [0 => 'Inactive', 1 => 'Active', 2 => 'Deleted'];
        $paymentMode = [1 => "cash", 2 => "cheque", 3 => "upi"];
        $transactionStatusLabels = [0 => 'Pending', 1 => 'Remaining', 2 => 'Complete'];
        $qty=0;
        $orderStatusLabels = [
            0 => 'Pending',
            1 => 'To Deliver',
            2 => 'Pickup',
            3 => 'Deliver',
            4 => 'Cancel',
            5 => 'Return',
            6 => 'Assign'
        ];
        $user = Seller::with(['state', 'city', 'area','order.transaction','order.transaction.histories','order','order.orderDetails','order.orderDetails.product'])->findOrFail($request->seller_id);
         $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'shop_name' => $user->shop_name,
            'seller_id' => $user->seller_id,
            'mobile' => $user->mobile,
            'whatsapp_no' => $user->whatsapp_no,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'status' => $statusLabels[$user->status] ?? '',
            'profile_pic' => !empty($user->profile_pic) ? asset('storage/uploads/profile/' . $user->profile_pic) : asset('profile/ic_profile_fall_back.jpg'),
            'addhar_front_pic' => !empty($user->addhar_front_pic) ? asset('storage/uploads/aadhar/' . $user->addhar_front_pic) : '',
            'addhar_back_pic' => !empty($user->addhar_back_pic) ? asset('storage/uploads/aadhar/' . $user->addhar_back_pic) : '',
            'city' => $user->city->name ?? '',
            'state' => $user->state->name ?? '',
            'address' => $user->address?? '',
            'beat' => $user->area->name ?? '',
            'orders' => $user->order->map(function ($order) use ($orderStatusLabels, $paymentMode, $transactionStatusLabels,$qty) {
                return [
                    'id' => $order->id,
                    'order_no' => $order->order_id,
                    'order_date' => $order->created_at,
                    'total_amount' => $order->total_price ?? '',
                    'discount' => $order->discount ?? '',
                    'order_status' => $orderStatusLabels[$order->status] ?? '',
                    'invoice_link' => route('downloadPdf',[Crypto::encryptId($order->id)]),

                   'products' => collect($order->orderDetails)->map(function ($orderDetail)  {
    $product = $orderDetail->product;
    if (!$product) {
        return null; // or you can return an empty array or message
    }

    return [
        'id' => $product->id ?? '',
        'name' => $product->name ?? '',
        'price' => $product->price ?? '',
        'hsn_code' => $product->hsn_code ?? '',
        'status' => !empty($product->status) && $product->status == 1 ? "Active" : "Inactive",
        'description' => $product->description ?? '',
        'category' => $product->category->name ?? '',
        'image' => !empty($product->image)
            ? asset("storage/uploads/product/" . $product->image)
            : "",
        'varient' => isset($product->getdetail) && is_iterable($product->getdetail)
            ? collect($product->getdetail)->map(function ($detail,$key) use($orderDetail)   {
                $price = $detail->retailer_price ?? 0;
                $mrp = $detail->mrp ?? 0;
                $discountPercentage = $mrp > 0 ? round(($price / $mrp) * 100, 2) : 0;
                $inclusivePrice = $price;
                $gstRate = $detail->gst ?? 0;

                if ($gstRate > 0) {
                    $basePrice = round($inclusivePrice / (1 + ($gstRate / 100)), 2);
                    $cgst = $sgst = round($gstRate / 2, 2);
                } else {
                    $basePrice = $inclusivePrice;
                    $cgst = $sgst = 0;
                }
                $qty = $orderDetail->where('varient_id',$detail->varient->id)->pluck('qty');
                return [
                    'id' => $detail->varient->id ?? '',
                    'qty'=>$qty[$key] ?? 0,
                    'price' => $price,
                    'mrp' => $mrp,
                    'gst' => $gstRate,
                    'cgst' => $cgst,
                    'sgst' => $sgst,
                    'exculsive_amount' => $basePrice,
                    'discount_percentage' => $discountPercentage,
                    'name' => $detail->varient
                        ? $detail->varient->name . ' ' . ($detail->varient->unit->name ?? '')
                        : '',
                ];
            })
            : [],
    ];
}),



                    'transaction' => collect($order->transaction)->map(function ($txn) use ($paymentMode, $transactionStatusLabels) {
                        return [
                            'id' => $txn->id,
                            'amount' => $txn->deduct_amount ?? '',
                            'payment_mode' => $paymentMode[$txn->payment_mode] ?? '',
                            'transaction_status' => $transactionStatusLabels[$txn->status] ?? '',
                            'date' => $txn->date ?? '',
                            'transaction_history' => collect($txn->histories)->map(function ($history) {
                                return [
                                    'id' => $history->id,
                                    'amount' => $history->deduct_amount ?? '',
                                    'date' => $history->date ?? '',
                                ];
                            }),
                        ];
                    }),
                ];
            })
        ];


        // Logging and response
        ApiLogService::success(Message::STAFF_SUCCESS, $userData);
        return ApiResponseService::success(Message::STAFF_LIST_SUCCESS, $userData);

    } catch (\Exception $e) {
        ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
        return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
    }
}




}
