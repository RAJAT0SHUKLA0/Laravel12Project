<?php

namespace App\Repositories\Location;

use App\Models\Area;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Models\Location;
use App\Utils\Crypto;

use App\Repositories\Location\LocationRepositoryInterface;

class LocationRepository implements LocationRepositoryInterface
{
    
    
    
    public function getAll()
    {
        return Location::query();  
    }

    public function statusupdate($id,$status)
    {
       $areaDataSet = Location::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
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
