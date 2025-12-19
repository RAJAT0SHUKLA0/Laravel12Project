<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leave;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use App\Helper\Message;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;
use App\Services\FcmService;
use App\Notifications\Payloads\LeavePayload;

class LeaveController extends Controller
{
      protected $fcm;

    public function __construct(FcmService $fcm)
    {
        $this->fcm = $fcm;
    }
    
    public function addLeaves(Request $request){
        
        try {
            ApiLogService::info('Leave request received', $request->all()); 
            $validator = Validator::make($request->all(), [
                'start_date' => 'required',
                'end_date' => 'required',
                'leave_type'=> 'required',
                'remark' => 'required',
                'userId' => 'required|integer|exists:tbl_users,id'
            ]);
             
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }
            $userExist = User::where('id',$request->userId)->select('staff_id','name','device_id')->first();
            $lastLeave = Leave::orderBy('id', 'desc')->first();
            $lastId = $lastLeave ? (int) filter_var($lastLeave->leave_id, FILTER_SANITIZE_NUMBER_INT) : 0;
            $leave_id = 'LID' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

            $dataArray = array('start_date'=>Carbon::parse($request->start_date)->format('Y-m-d'),'end_date'=>Carbon::parse($request->end_date)->format('Y-m-d'),'leave_type'=>$request->leave_type,'remark'=>$request->remark,'staff_id'=>$userExist['staff_id'],'leave_id'=>$leave_id,'user_id'=>$request->userId,'date'=>Carbon::now()->format('Y-m-d'));
            $Leave =Leave::create($dataArray);
            
            if($Leave){
                
                 // ✅ Push notification to requesting user
                if ($userExist->device_id) {
                    $route = 'view_leaves';
                    $payloadUser = LeavePayload::build(
                        $userExist->device_id,
                        "Leave Request Submitted",
                        "Dear {$userExist->name} Your leave request from {$request->start_date} to {$request->end_date} has been submitted.",
                        $route
                    );
                    $this->fcm->send($payloadUser);
                }

                // ✅ Push notification to Admin (user_id = 47)
                $admin = User::find(47);
                if ($admin && $admin->device_id) {
                     $route = 'leave_approval';
                    $payloadAdmin = LeavePayload::build(
                        $admin->device_id,
                        "New Leave Request",
                        "{$userExist->name} applied for leave from {$request->start_date} to {$request->end_date}.",
                        $route
                    );
                    $this->fcm->send($payloadAdmin);
                }
                
                ApiLogService::success(Message::LEAVE_SUCCESS, []);
                return ApiResponseService::success(Message::LEAVE_SUCCESS, []);  
            }else{
                ApiLogService::success(Message::LEAVE_UNSUCCESS, []);
                return ApiResponseService::success(Message::LEAVE_UNSUCCESS, []);
            }
            
            
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
     public function LeaveList(Request $request){
        try {
            ApiLogService::info('Leave request received', $request->all()); 
            $validator = Validator::make($request->all(), [
                'userId' => 'required|integer|exists:tbl_users,id'
            ]);
             
            $statusLabels = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];
            $leaveList = Leave::with('getLeaveType')->where('user_id', $request->userId)->orderBy('id', 'desc')
            ->get()
            ->map(function ($leave) use ($statusLabels) {
                $leave->status = $statusLabels[$leave->status] ?? 'Unknown';
                $leave->leave_type = $leave->getLeaveType->name ?? 'Unknown';
                unset($leave->getLeaveType);
                return $leave;
            });

            
            if($leaveList){
                ApiLogService::success(Message::LEAVE_SUCCESS_LIST, $leaveList);
                return ApiResponseService::success(Message::LEAVE_SUCCESS_LIST,$leaveList);  
            }else{
                ApiLogService::success(Message::LEAVE_UNSUCCESS_LIST, []);
                return ApiResponseService::success(Message::LEAVE_UNSUCCESS_LIST, []);
            }
            
            
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    public function LeaveTeamWiseList(Request $request){
        try {
            ApiLogService::info('Leave request received', $request->all()); 
            $statusLabels = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];
            $leaveList = Leave::with(['user.role']) 
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
                 $start = Carbon::parse($leave->start_date);
                 $formattedstartDate = $start->format('d F,Y');
                 $end = Carbon::parse($leave->end_date);
                 $formattedendDate = $end->format('d F,Y');
                 $leave->applied_date =  $formattedDate;
                 $days = $start->diffInDays($end)+ 1;
                 $leave->date =$formattedstartDate.' '.'-'.' '.$formattedendDate.' '.'('.$days.'Days)';
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
            
            
            if($leaveList){
                ApiLogService::success(Message::LEAVE_SUCCESS_LIST, $leaveList);
                return ApiResponseService::success(Message::LEAVE_SUCCESS_LIST,$leaveList);  
            }else{
                ApiLogService::success(Message::LEAVE_UNSUCCESS_LIST, []);
                return ApiResponseService::success(Message::LEAVE_UNSUCCESS_LIST, []);
            }
            
            
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
    
    public function LeaveStatusUpdate(Request $request)
    {
        
        try 
        {
             $validator = Validator::make($request->all(), [
                'staff_id' => 'required|integer|exists:tbl_users,id',
                'leave_id' =>'required|integer|exists:tbl_leaves,id'
            ]);
            ApiLogService::info('leave request received', $request->all());
            $leave = Leave::where('user_id', $request->staff_id)->where('id',$request->leave_id)->first();
            if ($leave) {
                $leave->status = $request->status;
                if($leave->save()){
                    
                     $userExist = User::find($leave->user_id);
                     // ✅ Push notification to requesting user
                    if ($userExist->device_id && $request->status == 1 ) {
                         $route = 'view_leaves';
                         $payloadUser = LeavePayload::build(
                        $userExist->device_id,
                        "Leave Request Approved",
                        "Dear {$userExist->name} Your leave request is approved from {$request->start_date} to {$request->end_date}.",
                        $route
                    );
                        $this->fcm->send($payloadUser);
                    }
                    
                    ApiLogService::success(Message::LEAVE_STATUS_SUCCESS, []);
                    return ApiResponseService::success(Message::LEAVE_STATUS_SUCCESS, []);  
                }else{
                    ApiLogService::success(Message::LEAVE_STATUS_UNSUCCESS, []);
                    return ApiResponseService::success(Message::LEAVE_STATUS_UNSUCCESS, []);
                }
            }
            
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
