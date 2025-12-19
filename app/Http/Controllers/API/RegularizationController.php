<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use App\Helper\Message;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Regularize;
use Carbon\Carbon;
use App\Services\FcmService;
use App\Notifications\Payloads\RegularizePayload;

class RegularizationController extends Controller
{
      protected $fcm;

    public function __construct(FcmService $fcm)
    {
        $this->fcm = $fcm;
    }
    
    public function addRegularize(Request $request){
        try {
            ApiLogService::info('regularize request received', $request->all()); 
            $validator = Validator::make($request->all(), [
                'date' => 'required',
                'remark' => 'required',
                'userId' => 'required'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }
            $userExist = User::where('id',$request->userId)->select('staff_id','name','device_id')->first();
            $dataArray = array('date'=>Carbon::parse($request->date)->format('Y-m-d'),'remark'=>$request->remark,'staff_id'=>$userExist['staff_id'],'user_id'=>$request->userId);
            $Regularize =Regularize::create($dataArray);
            
            if($Regularize){
                
                   // ✅ Push notification to requesting user
                if ($userExist->device_id) {
                    $route = 'regularization_requests';
                    $payloadUser = RegularizePayload::build(
                        $userExist->device_id,
                        "Regularization  Request Submitted",
                        "Dear {$userExist->name} Your Regularization request for date {$request->date} has been submitted.",
                        $route
                    );
                    $this->fcm->send($payloadUser);
                }

                // ✅ Push notification to Admin (user_id = 47)
                $admin = User::find(47);
                if ($admin && $admin->device_id) {
                    $route = 'regularize_approval';
                    $payloadAdmin = RegularizePayload::build(
                        $admin->device_id,
                        "New Regularization  Request",
                        "{$userExist->name} applied for Regularization on date {$request->date}.",
                        $route
                    );
                    $this->fcm->send($payloadAdmin);
                }
                
                ApiLogService::success(Message::REGULARIZE_SUCCESS, []);
                return ApiResponseService::success(Message::REGULARIZE_SUCCESS, []);  
            }else{
                ApiLogService::success(Message::REGULARIZE_UNSUCCESS, []);
                return ApiResponseService::success(Message::REGULARIZE_UNSUCCESS, []);
            }
            
            
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
     public function regularizeList(Request $request){
        try {
            ApiLogService::info('regularize request received', $request->all()); 
            $statusLabels = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];
            $RegularizeList = Regularize::where('user_id', $request->userId)->orderBy('id','desc')
            ->get()
            ->map(function ($leave) use ($statusLabels) {
                $leave->status = $statusLabels[$leave->status] ?? 'Unknown';
                return $leave;
            });

            if($RegularizeList){
                ApiLogService::success(Message::REGULARIZE_SUCCESS_LIST, $RegularizeList);
                return ApiResponseService::success(Message::REGULARIZE_SUCCESS_LIST,$RegularizeList);  
            }else{
                ApiLogService::success(Message::REGULARIZE_UNSUCCESS_LIST, []);
                return ApiResponseService::success(Message::REGULARIZE_UNSUCCESS_LIST, []);
            }
            
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
     public function regularizeTeamWiseList(Request $request){
        try {
            ApiLogService::info('regularize request received', $request->all()); 
            $statusLabels = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];
           $RegularizeList = Regularize::with(['user.role']) 
            ->whereHas('user', function ($query) {
                $query->where('role_id', '!=', 1);
            })
            ->orderByDesc('id')
            ->get()
            ->map(function ($leave) use ($statusLabels) {
                 $leave->status = $statusLabels[$leave->status] ?? 'Unknown';
                 $date = $leave->date ? $leave->date: $leave->created_at;
                 $date = Carbon::parse($date);
                 $formattedDate = $date->format('d F,Y');
                 $leave->date =$formattedDate;
                 if ($leave->user) {
                    $leave->name = $leave->user->name ?? '';
                    $leave->role = $leave->user->role->name ?? '';
                } else {
                    $leave->name = '';
                    $leave->role = '';
                }
                 unset($leave->user);
                 return $leave;
            });

            if($RegularizeList){
                ApiLogService::success(Message::REGULARIZE_SUCCESS_LIST, $RegularizeList);
                return ApiResponseService::success(Message::REGULARIZE_SUCCESS_LIST,$RegularizeList);  
            }else{
                ApiLogService::success(Message::REGULARIZE_UNSUCCESS_LIST, []);
                return ApiResponseService::success(Message::REGULARIZE_UNSUCCESS_LIST, []);
            }
            
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
    public function RegularizeStatusUpdate(Request $request)
    {
        try 
        {
            ApiLogService::info('regularize request received', $request->all());
            $regularize = Regularize::where('user_id', $request->staff_id)->where('id',$request->regularize_id)->first();
             
            if ($regularize) {
                $regularize->status = $request->status;
                if($regularize->save()){
                    
                    $userExist = User::find($regularize->user_id);
                       // ✅ Push notification to requesting user
                if ($userExist->device_id) {
                    $route = 'regularization_requests';
                    $payloadUser = RegularizePayload::build(
                        $userExist->device_id,
                        "Regularization  Request Approved",
                        "Dear {$userExist->name} Your Regularization request is approved for date {$regularize->date}.",
                        $route
                    );
                    $this->fcm->send($payloadUser);
                }
                    
                    ApiLogService::success(Message::REGULARIZE_STATUS_SUCCESS, []);
                    return ApiResponseService::success(Message::REGULARIZE_STATUS_SUCCESS, []);  
                }else{
                    ApiLogService::success(Message::REGULARIZE_STATUS_UNSUCCESS, []);
                    return ApiResponseService::success(Message::REGULARIZE_STATUS_UNSUCCESS, []);
                }
            }
            
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    
    
}
