<?php

namespace App\Repositories\State;

use App\Repositories\State\StateRepositoryInterface;
use App\Models\State;
use App\Utils\Crypto;
class StateRepository implements StateRepositoryInterface
{
    public function getAll()
    {
      return State::where('is_delete',0)->get();
    }
    public function find($id)
    {
        $stateId =Crypto::decryptId($id);
       return State::where('id',$stateId)->first();  
    }

    public function create(array $data)
    {
       $stateDataSet = State::create($data);
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
    }
    public function update($id, array $data)
    {
       $stateDataSet = State::where('id',$id)->update($data); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    public function delete($id)
    {
      $stateDataSet = State::where('id',$id)->update(['is_delete'=>1]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }  
    }
}
