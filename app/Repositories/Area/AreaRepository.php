<?php

namespace App\Repositories\Area;

use App\Models\Area;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Utils\Crypto;
use App\Repositories\Area\AreaRepositoryInterface;

class AreaRepository implements AreaRepositoryInterface
{
    
    public function getAll()
    {
        return Area::where('is_delete',1)->get();  
    }


    public function find($id)
    {
        
        $AreaId = Crypto::decryptId($id);
       return Area::where('id',$AreaId)->first();  
    }

    public function create(array $data)
    {
         
       
        $areaDataSet = Area::create($data);
        return $areaDataSet ? true : false;
    }

    public function getState()
    {
        return State::get();
    }

    public function getCity($state_id)
    {
        return City::where('state_id', $state_id)->get();
    }
    
     public function delete($id)
    {
      $areaDataSet = Area::where('id',$id)->update(['is_delete'=>0]); 
       if($areaDataSet){
         return true;
       }else{
         return false;
       }  
    }
    
    
    public function getAllCity(){
        return City::get();
    }
    
     public function update($id, array $data)
    {
       $areaDataSet = Area::where('id',$id)->update($data); 
       if($areaDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    
}
