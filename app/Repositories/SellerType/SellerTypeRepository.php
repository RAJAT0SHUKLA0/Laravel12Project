<?php

namespace App\Repositories\SellerType;

use App\Repositories\SellerType\SellerTypeRepositoryInterface;
use App\Models\SellerType;
use App\Utils\Crypto;
class SellerTypeRepository implements SellerTypeRepositoryInterface
{
    public function getAll()
    {
      return SellerType::where('is_delete',0)->get();
    }
    public function find($id)
    
    {
        $sellerTypeId =Crypto::decryptId($id);
       return SellerType::where('id',$sellerTypeId)->first();  
    }

    public function create(array $data)
    {
       $stateDataSet = SellerType::create($data);
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
    }
    public function update($id, array $data)
    {
       $stateDataSet = SellerType::where('id',$id)->update($data); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    public function delete($id)
    {
      $stateDataSet = SellerType::where('id',$id)->update(['is_delete'=>1]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }  
    }
}
