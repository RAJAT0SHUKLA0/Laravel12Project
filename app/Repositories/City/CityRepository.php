<?php

namespace App\Repositories\City;

use App\Models\City;
use App\Models\State;
use App\Utils\Crypto;
use App\Repositories\City\CityRepositoryInterface;
class CityRepository implements CityRepositoryInterface
{
    public function getAll()
    {
      return City::with('getState')->where ('is_delete',0)->get();  
    }
    public function find($id)
    {
        
        $cityId= Crypto::decryptId($id) ;
       return City::where('id',$cityId)->first(); 
      
    }

    public function create(array $data)
    {
       $stateDataSet = City::create($data);
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
    }
    public function update($id, array $data)
    {
       $stateDataSet = City::where('id',$id)->update($data); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    public function delete($id)
    {
      $stateDataSet = City::where('id',$id)->update(['is_delete'=>1]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }  
    }
    
      public function getState()
    {
        return State::get();
    }

}
