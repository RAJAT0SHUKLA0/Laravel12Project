<?php

namespace App\Repositories\Staff;

use App\Models\City;
use App\Models\State;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Staff\StaffRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Utils\Uploads;
use App\Helper\Message;
use App\Services\ApiLogService;
use App\Utils\Crypto;

class StaffRepository implements StaffRepositoryInterface
{
    public function query()
    {
      return User::query();  
    }
    public function find($id)
    {
        $staffId =Crypto::decryptId($id);
       return User::where('id',$staffId)->first();  
    }

    public function create(array $data)
    {   try {
         $user = User::orderby('id','desc')->whereNotNull('staff_id')->first();
        if (!empty($user)) {
            $lastStaffId = ltrim($user->staff_id, '0'); 
            $exploadData=explode('-',$lastStaffId);
            $lastStaffId = is_string($lastStaffId) ? (int)$exploadData[1] : 0;
            $nextStaffId = str_pad($lastStaffId + 1, 3, '0', STR_PAD_LEFT);
            $data['staff_id'] = $exploadData[0].'-'.$nextStaffId;
        }

        if (isset($data['profile_pic']) && $data['profile_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['profile_pic'] = Uploads::uploadImage($data['profile_pic'], 'profile', 'profile_pic');
        }
        if (isset($data['addhar_front_pic']) && $data['addhar_front_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['addhar_front_pic'] = Uploads::uploadImage($data['addhar_front_pic'], 'aadhar', 'aadhar_front');
        }
        if (isset($data['addhar_back_pic']) && $data['addhar_back_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['addhar_back_pic'] = Uploads::uploadImage($data['addhar_back_pic'], 'aadhar', 'aadhar_back');
        }
        
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
            $user = User::create($data);
        return $user ? true : false;
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
    }

    public function update($id, array $data)
    {
        
        try {
        if (isset($data['profile_pic']) && $data['profile_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['profile_pic'] = Uploads::uploadImage($data['profile_pic'], 'profile', 'profile_pic');
        }
        if (isset($data['addhar_front_pic']) && $data['addhar_front_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['addhar_front_pic'] = Uploads::uploadImage($data['addhar_front_pic'], 'aadhar', 'aadhar_front');
        }
        if (isset($data['addhar_back_pic']) && $data['addhar_back_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['addhar_back_pic'] = Uploads::uploadImage($data['addhar_back_pic'], 'aadhar', 'aadhar_back');
        }
    
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user =User::where('id',$id)->update($data);
        return $user ? true : false;
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
        
    }
    public function delete($id)
    {
      $stateDataSet = User::where('id',Crypto::decryptId($id))->update(['status'=>3]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }  
    }
    
    
    public function statusupdate($id,$status){
       $stateDataSet = User::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
    
    public function changePassword($id,$password){
       $newPaasword =Hash::make($password);
       $changePassword = User::where('id',Crypto::decryptId($id))->update(['password'=>$newPaasword]); 
       if($changePassword){
         return true;
       }else{
         return false;
       }   
    }
    
    
    public function isLocationEnable($id,$status){
        $stateDataSet = User::where('id',$id)->update(['is_location_enable'=>$status]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }   
    }
    public function getState()
    {
      return  State::get();
    }
    
    public function getCity($state_id)
    {
      return City::where('state_id',$state_id)->get();
    }
    public function getRole()
    {
       return  Role::whereNotIn('id',[1])->get();
    }

    public function getAllCity(){
        return City::get();
    }
}
