<?php

namespace App\Repositories\Regularize;

use App\Repositories\Regularize\RegularizeRepositoryInterface;

use App\Models\User;
use App\Models\Regularize;
use App\Utils\Crypto;

class RegularizeRepository implements RegularizeRepositoryInterface
{
    public function getAll()
    {
      return Regularize::query();  
    }
    
    public function Regularizestatusupdate($id,$status){
       $stateDataSet = Regularize::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
   
    
}
