<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Location;
use App\Helper\Message;
use App\Models\LocationSetting;
use App\Models\Attendance;


class LocationController extends Controller
{
     
    public function getUserLocation(Request $request){
        try {
            ApiLogService::info('Attendance request received', $request->all());
            $validator = Validator::make($request->all(), [
                'userId' => 'required|integer|exists:tbl_users,id',
                'latitude'=> 'required',
                'longitude' => 'required',
                'address'   => 'required'
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }
            $date   = Carbon::now()->format('Y-m-d');
            $data =array('user_id'=>$request->userId,"latitude"=>$request->latitude,"longitude"=>$request->longitude,'date'=>$date,'address'=>$request->address,'battery_level'=>$request->battery_level,'accuracy_meter'=>$request->accuracy_meter,'accuracy_status'=>$request->accuracy_status);
            $Location = Location::create($data);
            if($Location){
                ApiLogService::success(Message::LOCATION_SUCCESS, []);
                return ApiResponseService::success(Message::LOCATION_SUCCESS, []);  
            }else{
                ApiLogService::success(Message::REGULARIZE_UNSUCCESS, []);
                return ApiResponseService::success(Message::LOCATION_UNSUCCESS, []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
    public function loadLocationConfig(Request $request){
        try {
            $LocationSetting = LocationSetting::latest()->first();
            $userId = auth()->id();
            $is_service_enable = false;
            if ($request->has('all_location_enable') && $request->all_location_enable === 'true') {
                $is_service_enable = !empty($LocationSetting->service_enabled) && $LocationSetting->service_enabled == 1;
            }
            if (auth()->check() && isset(auth()->user()->is_location_enable) && !is_null(auth()->user()->is_location_enable)) {
                $is_service_enable = auth()->user()->is_location_enable ==1;
            }
            $update_interval_seconds = $LocationSetting->update_interval_seconds ?? '';
            $today = Carbon::today();
            $isUserWorking = false;
            $level = 'high';
    
            $Location = Location::where('user_id', $userId)->latest()->first();
            if (!empty($Location->battery_level)) {
                if ($Location->battery_level <= 10) {
                    $level = 'off';
                } elseif ($Location->battery_level <= 25) {
                    $level = 'low';
                } elseif ($Location->battery_level <= 50) {
                    $level = 'medium';
                } else {
                    $level = 'high';
                }
            }
            $isUserWorking = Attendance::where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->whereNotNull('in_time')
                ->whereNull('out_time')
                ->exists();
    
            $data = [
                'service_enabled' => $is_service_enable,
                'update_interval_seconds' => $update_interval_seconds,
                'is_user_working' => $isUserWorking,
                'priority' => $level
            ];
            ApiLogService::success(Message::LOCATION_CONFIG_SUCCESS, $data);
            return ApiResponseService::success(Message::LOCATION_CONFIG_SUCCESS, $data);
    
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage());  
        }
    }
}
