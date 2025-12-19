<?php

namespace App\Repositories\Attendance;

use App\Models\Area;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Models\Attendance;
use App\Utils\Crypto;

use App\Repositories\Attendance\AttendanceRepositoryInterface;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    
    
    
    public function getAll()
    {
        return Attendance::query();  
    }

    public function statusupdate($id,$status)
    {
       $areaDataSet = Attendance::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($areaDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    
    public function getUserAll()
    {
        return User::whereNotIn('role_id',[1])->get();  
    }
    
}
