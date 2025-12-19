<?php

namespace App\Repositories\Leave;

use App\Repositories\Leave\LeaveRepositoryInterface;
use App\Models\Leave;
use App\Models\User;
use App\Utils\Crypto;
class LeaveRepository implements LeaveRepositoryInterface
{
    public function getAll()
    {
      return Leave::query();  
    }
     public function leavestatusupdate($id,$status){
       $stateDataSet = Leave::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
   
    
}
