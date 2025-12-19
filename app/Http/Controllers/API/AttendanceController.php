<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helper\Message;
use App\Http\Resources\AttendanceResource;
use App\Models\Leave;


class AttendanceController extends Controller
{
    public function markAttendance(Request $request)
    {
        try {
            ApiLogService::info('Attendance request received', $request->all());
            $validator = Validator::make($request->all(), [
                'markType' => 'required',
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
              $markType = $request->input('markType');
              $date   = Carbon::now()->format('Y-m-d');
              $inTime = Carbon::now()->format('H:i:s');
            if ($markType == 'in') {
                $allAttendance = Attendance::where('user_id', Auth::id())->whereDate('date', $date)->latest()->first();
                if (!empty($allAttendance) && $allAttendance->in_time_status == 1) {
                    ApiLogService::info('Already in time marked.', $allAttendance);
                    return response()->json(['status' => false, 'message' => 'Already in time marked.'], 400);
                }
                $attendance = Attendance::create(['user_id' => $request->userId,'in_time' => $inTime,'date'=> $date,'in_time_status'=>1,"in_time_lat"=>$request->latitude,"in_time_long"=>$request->longitude,"in_time_address"=>$request->address]);
                ApiLogService::success(Message::ATTENDANCE_START_TIME_MESSAGE, $allAttendance);
                return ApiResponseService::success(Message::ATTENDANCE_START_TIME_MESSAGE, new AttendanceResource($attendance));
    
            } else {
                $attendance = Attendance::where('user_id', Auth::id())
                    ->whereDate('date', $date)
                    ->latest()
                    ->first();
                if (!empty($attendance) && $attendance->out_time_status == 1) {
                    ApiLogService::info('Already out time marked.', $attendance);
                    return response()->json(['status' => false, 'message' => 'Already out time marked.'], 400);
                }
                $inTimedata  = Carbon::parse($attendance->in_time);
                $outTime = Carbon::parse($inTime);
                $workingHours = $inTimedata->diff($outTime);
                $totalHours = $workingHours->format('%H:%I');
                $attendance->update(['out_time' => $inTime,'working_hours'=>$totalHours,'out_time_status'=>1,"out_time_lat"=>$request->latitude,"out_time_long"=>$request->longitude,"out_time_address"=>$request->address]);
                ApiLogService::success(Message::ATTENDANCE_END_TIME_MESSAGE, $attendance);
                return ApiResponseService::success(Message::ATTENDANCE_END_TIME_MESSAGE, new AttendanceResource($attendance));
            }
    
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    
    
    
    public function checkAttendanceStatus(Request $request){
        try
        {
            $intimeStatus= false;
            $outtimeStatus =false;
            ApiLogService::info('Attendance request received', $request->all());
            $query =Attendance::query();
            $date   = Carbon::now()->format('Y-m-d');
            
            // $Leave = Leave::where('date',$date)->where('user_id', $request->userId)->first();
            // if($Leave){
            //     ApiLogService::success('leabve List  Found!!', $Leave);
            //     return ApiResponseService::error('You Have ALready Applied for Leave', []);
            // }
            
            $attendanceList = $query->where('user_id', $request->userId)->whereDate('date', $date)->orderBy('date', 'asc')->select('in_time','out_time')->latest()->first();
            if(!empty($attendanceList->in_time)){
                $intimeStatus =true;
            }
            if(!empty($attendanceList->out_time)){
                $outtimeStatus =true;
            }
            $statusData=array("in_time"=>$attendanceList->in_time??'No data available','out_time'=>$attendanceList->out_time??'No data available','in_time_status'=>$intimeStatus,'out_time_status'=>$outtimeStatus,'locationTime'=>config('constants.LOCATION_TRACK_TIME'));
            if(!empty($statusData)){
                ApiLogService::success('attendance status details', $statusData);
                return ApiResponseService::success('attendance status details', $statusData);
            }else{
                ApiLogService::success('attendance List not Found!!', []);
                return ApiResponseService::error('attendance List not Found !!', []);
            }
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
       
        
    }
    
    
    public function attendanceList(Request $request){
        try
        {
            ApiLogService::info('Attendance request received', $request->all());
            $query =Attendance::query();
            $month = Carbon::now()->month;
            if(!empty($month)){
              $query->whereMonth('date',$month);  
            }
            
            $attendanceList = $query->where('user_id', $request->userId)->orderBy('date', 'desc')->get();
            if(!empty($attendanceList)){
                ApiLogService::success('attendance List Found', $attendanceList);
                return ApiResponseService::success('attendance List Found', $attendanceList);
            }else{
                ApiLogService::success('attendance List not Found!!', []);
                return ApiResponseService::error('attendance List not Found !!', []);
            }
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
       
        
    }
    
    
     public function clearAttendance(Request $request)
    {
        try {
            $date   = Carbon::now()->format('Y-m-d');
            $attendance = Attendance::where('user_id', $request->userId)
                    ->delete();
            return ApiResponseService::success('clear successfully', []);
        } catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
}

