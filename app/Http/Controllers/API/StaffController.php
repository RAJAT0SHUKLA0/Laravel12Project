<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiLogService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Helper\Message;
use App\Utils\Uploads;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function AddStaff(Request $request)
    {
        try {
            ApiLogService::info('staff request received', $request->all());
            $validator = Validator::make($request->all(), [
                'name'            =>     'required',
                'mobile'          =>      'required|unique:tbl_users,mobile',
                'email'            =>     'required',
                'state'           =>       'required',
                'city'            =>        'required',
                'joining_date'    =>         'required',
                'profile_pic'     =>          'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
                'aadhar_front_pic' =>          'required|file|mimes:jpg,jpeg,png,webp|max:2048',
                'aadhar_back_pic'  =>      'required|file|mimes:jpg,jpeg,png,webp|max:2048',
                'role'             =>       'required',


            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }
             $staff_id ='';
            $user = User::orderby('id','desc')->whereNotNull('staff_id')->first();
            if (!empty($user)) {
                $lastStaffId = ltrim($user->staff_id, '0');
                $exploadData=explode('-',$lastStaffId);
                $lastStaffId = is_string($lastStaffId) ? (int)$exploadData[1] : 0;
                $nextStaffId = str_pad($lastStaffId + 1, 3, '0', STR_PAD_LEFT);
                $staff_id = $exploadData[0].'-'.$nextStaffId;
            }
            $User  = new User;
            $User->name =$request->name;
            $User->mobile = $request->mobile;
            $User->email = $request->email;
            $User->state_id = $request->state;
            $User->city_id = $request->city;
            $User->joining_date = Carbon::parse($request->joining_date)->format('Y-m-d');
            $User->role_id =$request->role;
            $User->staff_id =$staff_id;
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
    
    public function staffList()
    {
        try {
           
           $statusLabels = [
                0 => 'Inactive',
                1 => 'Active',
                2 => 'Deleted',
            ];
            $user = User::with(['role','state','city'])->orderby('updated_at','desc')->whereNotIn('role_id',[1])->get()->map(function ($users) use ($statusLabels) {
                $users->status = $statusLabels[$users->status] ?? 'Unknown';
                $users->role_id = $users->role->name ?? '';
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
    
    public function staffDetail(Request $request)
    {
        try {
             $request->validate([
                'staff_id' => 'required|integer|exists:tbl_users,id'
            ]);
           ApiLogService::info('staff request received', $request->all());
           $statusLabels = [
                0 => 'Inactive',
                1 => 'Active',
                2 => 'Deleted',
            ];
            
            $user = User::findOrFail($request->staff_id);
            $user->status = $statusLabels[$user->status] ?? 'Unknown';
            $user->role_id = $user->role->name ?? '';
            $user->city_id  =         !empty($user->city->name)?$user->city->name:'';
                $user->state_id  =      !empty($user->state->name)?$user->state->name:'';
             $user->profile_pic = !empty($users->profile_pic)?asset('storage/uploads/profile/'.$user->profile_pic):asset('profile/ic_profile_fall_back.jpg');
                $user->addhar_front_pic =  !empty($users->addhar_front_pic)?asset('storage/uploads/aadhar/'.$user->addhar_front_pic):'';
                $user->addhar_back_pic = !empty($users->addhar_back_pic)?asset('storage/uploads/aadhar/'.$user->addhar_back_pic):'';
                  unset($user->state);
                unset($user->city);
            unset($user->role);
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
    
    public function UpdateStaff(Request $request)
    {
        try {
            ApiLogService::info('staff request received', $request->all());
            $validator = Validator::make($request->all(), [
                'name'            =>     'required',
                'email'            =>     'required',
                'state'           =>       'required',
                'city'            =>        'required',
                'joining_date'    =>         'required',
                'profile_pic'     =>          'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
                'aadhar_front_pic' =>          'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
                'aadhar_back_pic'  =>      'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
                'role'             =>       'required',
                'staff_id'       =>      'required|integer|exists:tbl_users,id'


            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                ApiLogService::warning(Message::VALIDATION_MESSAGE, $errors);
                return ApiResponseService::validation(Message::VALIDATION_MESSAGE, $errors);
            }
            $User = User::findOrFail($request->staff_id)->orderby('id','desc');
            $User->name =$request->name;
            $User->email = $request->email;
            $User->state_id = $request->state;
            $User->city_id = $request->city;
            $User->joining_date = Carbon::parse($request->joining_date)->format('Y-m-d');
            $User->role_id =$request->role;
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
    
    public function staffStatusUpdate(Request $request)
    {
        try 
        {
              $request->validate([
                'staff_id' => 'required|integer|exists:tbl_users,id'
            ]);
            ApiLogService::info('staff request received', $request->all());
            $user = User::findOrFail($request->staff_id);
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
}
