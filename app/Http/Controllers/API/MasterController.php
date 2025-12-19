<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Message;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use App\Models\State;
use App\Models\City;
use App\Models\Role;
use App\Models\Area;
use App\Models\LeaveType;
use App\Models\SellerType;
use App\Models\User;
use App\Models\Order;
use App\Models\{Regularize,Leave,Seller};


class MasterController extends Controller
{
    public function getState(){
        try {
        
            $getState = State::where('is_delete','!=',1)->orderBy('name','asc')->select('id','name')->get();
            if($getState){
                ApiLogService::success(sprintf(Message::MASTER_SUCCESS,'state'), $getState);
                return ApiResponseService::success(sprintf(Message::MASTER_SUCCESS,'state'), $getState);  
            }else{
                ApiLogService::success(sprintf(Message::MASTER_UNSUCCESS,'state'), []);
                return ApiResponseService::success(sprintf(Message::MASTER_UNSUCCESS,'state'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    public function getCity(Request $request){
        try {
        
            $getCity = City::where('is_delete','!=',1)->where('state_id',$request->state_id)->orderBy('name','asc')->select('id','name')->get();
            if($getCity){
                ApiLogService::success(sprintf(Message::MASTER_SUCCESS,'city'), $getCity);
                return ApiResponseService::success(sprintf(Message::MASTER_SUCCESS,'city'), $getCity);  
            }else{
                ApiLogService::success(sprintf(Message::MASTER_UNSUCCESS,'city'), []);
                return ApiResponseService::success(sprintf(Message::MASTER_UNSUCCESS,'city'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    public function getBeatCityWise(Request $request){
        try {
        
            $getBeat = Area::where('city_id',$request->city_id)->orderBy('name','asc')->select('id','name')->get();
            if($getBeat){
                ApiLogService::success(sprintf(Message::MASTER_SUCCESS,'beat'), $getBeat);
                return ApiResponseService::success(sprintf(Message::MASTER_SUCCESS,'beat'), $getBeat);  
            }else{
                ApiLogService::success(sprintf(Message::MASTER_UNSUCCESS,'beat'), []);
                return ApiResponseService::success(sprintf(Message::MASTER_UNSUCCESS,'beat'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
     public function getBeat(Request $request){
        try {
        
            $getBeat = Area::orderBy('name','asc')->select('id','name')->get();
            if($getBeat){
                ApiLogService::success(sprintf(Message::MASTER_SUCCESS,'beat'), $getBeat);
                return ApiResponseService::success(sprintf(Message::MASTER_SUCCESS,'beat'), $getBeat);  
            }else{
                ApiLogService::success(sprintf(Message::MASTER_UNSUCCESS,'beat'), []);
                return ApiResponseService::success(sprintf(Message::MASTER_UNSUCCESS,'beat'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    public function getRole(){
        try {
        
            $getRole = Role::whereNotIn('id',[1])->select('id','name')->get();
            if($getRole){
                ApiLogService::success(sprintf(Message::MASTER_SUCCESS,'role'), $getRole);
                return ApiResponseService::success(sprintf(Message::MASTER_SUCCESS,'role'), $getRole);  
            }else{
                ApiLogService::success(sprintf(Message::MASTER_UNSUCCESS,'role'), []);
                return ApiResponseService::success(sprintf(Message::MASTER_UNSUCCESS,'role'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
   public function getMasterData(Request $request)
{
    try {
        $userId = $request->user_id;

        $data = State::where('is_delete', '!=', 1)
            ->select('id', 'name')
            ->with([
                'cities' => function ($query) {
                    $query->select('id', 'name', 'state_id')
                        ->with([
                            'beats' => function ($q) {
                                $q->select('id', 'name', 'city_id');
                            }
                        ]);
                }
            ])
            ->orderBy('name', 'asc')
            ->get();

        $preference = null;

        if ($userId) {
            $seller = User::where('id', $userId)->first();

            if ($seller) {
                $preference = [
                    'selectedState' => $seller->state_id ?? null,
                    'selectedCity'  => $seller->city_id ?? null,
                ];
            }
        }
        
        
        

        $response = [
            $data,
            'preference'  => $preference,
        ];

        ApiLogService::success(sprintf(Message::MASTER_SUCCESS, 'state-city-beat'), $response);

        return ApiResponseService::success(
            sprintf(Message::MASTER_SUCCESS, 'state, city & beat'),
            $response
        );

    } catch (\Exception $e) {
        ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
        return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
    }
}

    
    
    
    public function getLeaveType(){
        try {
        
            $getLeaveType = LeaveType::orderBy('name','asc')->select('id','name')->get();
            if($getLeaveType){
                ApiLogService::success(sprintf(Message::MASTER_SUCCESS,'LeaveType'), $getLeaveType);
                return ApiResponseService::success(sprintf(Message::MASTER_SUCCESS,'LeaveType'), $getLeaveType);  
            }else{
                ApiLogService::success(sprintf(Message::MASTER_UNSUCCESS,'LeaveType'), []);
                return ApiResponseService::success(sprintf(Message::MASTER_UNSUCCESS,'LeaveType'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
    public function getSellerType(){
        try {
        
            $getSellerType = SellerType::orderBy('name','asc')->select('id','name')->get();
            if($getSellerType){
                ApiLogService::success(sprintf(Message::MASTER_SUCCESS,'SellerType'), $getSellerType);
                return ApiResponseService::success(sprintf(Message::MASTER_SUCCESS,'LeaveType'), $getSellerType);  
            }else{
                ApiLogService::success(sprintf(Message::MASTER_UNSUCCESS,'SellerType'), []);
                return ApiResponseService::success(sprintf(Message::MASTER_UNSUCCESS,'LeaveType'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
    
    
    
    public function deleteRequest(Request $request)
    {
        
        try 
        {
            ApiLogService::info(' request received', $request->all());
            $models= Leave::query();
            $name="Leave";
            if($request->type==1){
                $models= Regularize::query();
                $name="Regularize";
            }
            $leave = $models->where('user_id', $request->staff_id)->where('id',$request->id)->delete();
            if ($leave) {
                
                    ApiLogService::success(sprintf(Message::LEAVE_DELETE_SUCCESS,$name), []);
                    return ApiResponseService::success(sprintf(Message::LEAVE_DELETE_SUCCESS,$name), []);  
                
            }else{
                    ApiLogService::success(sprintf(Message::LEAVE_DELETE_UNSUCCESS),$name, []);
                    return ApiResponseService::success(sprintf(Message::LEAVE_DELETE_UNSUCCESS,$name), []);
                }
            
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    
    public function getBeatAssignOrderWise(Request $request){
        try {
            $sellerId = Order::where('status','=',0)->select('seller_id')->get()->toArray();
            $beatId = Seller::whereIn('id',$sellerId)->select('beat_id')->get()->toArray();
            $getBeat = Area::whereIn('id',$beatId)->orderBy('name','asc')->select('id','name')->get();
            if($getBeat){
                ApiLogService::success(sprintf(Message::MASTER_SUCCESS,'beat'), $getBeat);
                return ApiResponseService::success(sprintf(Message::MASTER_SUCCESS,'beat'), $getBeat);  
            }else{
                ApiLogService::success(sprintf(Message::MASTER_UNSUCCESS,'beat'), []);
                return ApiResponseService::success(sprintf(Message::MASTER_UNSUCCESS,'beat'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    public function getRider(Request $request){
        try {
           
            $getRider = User::where('role_id',4)->where('status',1)->orderBy('name','asc')->select('id','name')->get();
            if($getRider){
                ApiLogService::success(sprintf(Message::MASTER_SUCCESS,'Rider'), $getRider);
                return ApiResponseService::success(sprintf(Message::MASTER_SUCCESS,'Rider'), $getRider);  
            }else{
                ApiLogService::success(sprintf(Message::MASTER_UNSUCCESS,'Rider'), []);
                return ApiResponseService::success(sprintf(Message::MASTER_UNSUCCESS,'Rider'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }

}
