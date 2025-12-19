<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Location;
use Illuminate\Pagination\Paginator;

use App\Helper\Message;
class TeamsController extends Controller
{
    public function teamWiseList(){
        try {
           $statusLabels = [
                0 => 'Inactive',
                1 => 'Active',
                2 => 'Deleted',
            ];
            $user = User::with('role')->orderby('id','desc')->whereNotIn('role_id',[1])->get()->map(function ($users) use ($statusLabels) {
                $users->status = $statusLabels[$users->status] ?? 'Unknown';
                $users->role_id = $users->role->name ?? 'Unknown';
                 $users->profile_pic      =      !empty($users->profile_pic)?asset('storage/uploads/profile/'.$users->profile_pic):asset('profile/ic_profile_fall_back.jpg');
                $users->addhar_front_pic =      !empty($users->addhar_front_pic)?asset('storage/uploads/aadhar/'.$users->addhar_front_pic):'';
                $users->addhar_back_pic  =      !empty($users->addhar_back_pic)?asset('storage/uploads/aadhar/'.$users->addhar_back_pic):'';
                 $users->city_id  =         !empty($users->city->name)?$users->city->name:'';
                $users->state_id  =      !empty($users->state->name)?$users->state->name:'';
                 unset($users->state);
                unset($users->city);
                unset($users->role);
                return $users;
            });
            
            if($user){
                ApiLogService::success(Message::TEAMS_SUCCESS, $user);
                return ApiResponseService::success(Message::TEAMS_SUCCESS, $user);  
            }else{
                ApiLogService::success(Message::TEAMS_UNSUCCESS, []);
                return ApiResponseService::success(Message::TEAMS_UNSUCCESS, []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    
    public function teamWiseAttendanceList(Request $request){
        try {
          ApiLogService::info('team wise  request received', $request->all());
          $validator = Validator::make($request->all(), [
                'staff_id'             =>       'required'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::error('Validation failed',$errors, 422);
            }
             $query =Attendance::query();
             if ($request->filled('month')) {
                 $month = $request->input('month');
                  $query->whereMonth('date',$month);  
             }else{
                 $query->whereMonth('date',Carbon::now()->month);
             }
            $attendance =$query->where('user_id',$request->staff_id)->orderBy("id","desc")->get();
            if($attendance){
                 ApiLogService::success(sprintf(Message::TEAMS_WISE_ATTENDANCE_LIST_SUCCESS,'attendance'), $attendance);
                return ApiResponseService::success(sprintf(Message::TEAMS_WISE_ATTENDANCE_LIST_SUCCESS,'attendance'), $attendance);   
            }else{
                ApiLogService::success(sprintf(Message::TEAMS_WISE_ATTENDANCE_LIST_UNSUCCESS,'attendance'), []);
                return ApiResponseService::success(sprintf(Message::TEAMS_WISE_ATTENDANCE_LIST_UNSUCCESS,'attendance'), []);
            }
            
        } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
        }
    }
    

        public function teamWiseLocationList(Request $request)
        {
            try {
                ApiLogService::info('Team-wise request received', $request->all());
        
                $validator = Validator::make($request->all(), [
                    'staff_id' => 'required|integer',
                    'limit'    => 'integer|min:1|max:100',
                    'page'     => 'integer|min:1',
                    'date'     => 'sometimes|date',
                ]);
        
                if ($validator->fails()) {
                    $errors = $validator->errors()->all();
                    ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                    return ApiResponseService::error('Validation failed', $errors, 422);
                }
        
                // Set page manually for POST (only needed if it's not working automatically)
                if ($request->filled('page')) {
                    Paginator::currentPageResolver(function () use ($request) {
                        return $request->input('page');
                    });
                }
        
                $limit = $request->input('limit', 10);
        
                $query = Location::where('user_id', $request->staff_id);
        
                if ($request->filled('date')) {
                    $query->whereDate('date', Carbon::parse($request->input('date'))->format('Y-m-d'));
                } else {
                    $query->whereDate('date', Carbon::now()->format('Y-m-d'));
                }
        
                $locations = $query->orderBy('id', 'desc')->paginate($limit);
        
                $locations->getCollection()->transform(function ($location) {
                    $location->created_date_time = Carbon::parse($location->created_at)->format('F j, Y g:i A');
                    $location->updated_date_time = Carbon::parse($location->updated_at)->format('F j, Y g:i A');
                    return $location;
                });
        
                ApiLogService::success(sprintf(Message::TEAMS_WISE_ATTENDANCE_LIST_SUCCESS, 'location'), $locations);
        
                return ApiResponseService::success(
                    sprintf(Message::TEAMS_WISE_ATTENDANCE_LIST_SUCCESS, 'location'),
                    $locations
                );
        
            } catch (\Exception $e) {
                ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
                return ApiResponseService::error(Message::SERVER_ERROR_MESSAGE, $e->getMessage(), 500);
            }
        }


}
