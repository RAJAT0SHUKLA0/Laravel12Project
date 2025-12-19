<?php

namespace App\Repositories\Varient;

use App\Models\Varient;
use App\Models\Unit;
use App\Utils\Crypto;
use App\Repositories\Varient\VarientRepositoryInterface;
class VarientRepository implements VarientRepositoryInterface
{
    public function getAll()
    {
      return Varient::with('unit')->where ('is_delete',0)->get();  
    }
    public function find($id)
    {
       $varientid =Crypto::decryptId($id);
       return Varient::where('id',$varientid)->first(); 
       
    }

    public function create(array $data)
    {
       $stateDataSet = Varient::create($data);
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
    }
    public function update($id, array $data)
    {
       $varientDataSet = Varient::where('id',$id)->update($data); 
       if($varientDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    
      public function statusupdate($id,$status){
       $stateDataSet = Varient::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
    
    
    
    public function delete($id)
    {
      $varientDataSet = Varient::where('id',Crypto::decryptId($id))->update(['is_delete'=>1]); 
       if($varientDataSet){
         return true;
       }else{
         return false;
       }  
    }
    
      public function getUnit()
    {
        return Unit::get();
    }

}
