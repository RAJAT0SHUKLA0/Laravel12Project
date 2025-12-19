<?php

namespace App\Repositories\Settings;

use App\Repositories\Settings\SubMenuTypeRepositoryInterface;
use App\Models\SubMenuType;
class SubMenuTypeRepository implements SubMenuTypeRepositoryInterface
{
    public function getAll()
    {
      return SubMenuType::where('is_delete',0)->get();
    }
    public function find($id)
    {
       return SubMenuType::where('id',$id)->first();  
    }

    public function create(array $data)
    {
       $stateDataSet = SubMenuType::create($data);
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
    }
    public function update($id, array $data)
    {
       $stateDataSet = SubMenuType::where('id',$id)->update($data); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    public function delete($id)
    {
      $stateDataSet = SubMenuType::where('id',$id)->update(['is_delete'=>1]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }  
    }
   
}
