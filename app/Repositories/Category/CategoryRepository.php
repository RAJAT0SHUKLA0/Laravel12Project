<?php

namespace App\Repositories\Category;

use App\Models\Category;
use App\Models\User;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Utils\Uploads;
use App\Helper\Message;
use App\Services\ApiLogService;
use App\Utils\Crypto;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll()
    {
      return Category::query();  
    }
    public function find($id)
    {
        $staffId =Crypto::decryptId($id);
       return Category::where('id',$staffId)->first();  
    }

    public function create(array $data)
    {   try {
       
        $user = Category::create($data);
        return $user ? true : false;
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
    }

    public function update($id, array $data)
    {
        
        try {
        
        $category =Category::where('id',$id)->update($data);
        return $category ? true : false;
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
        
    }
    public function delete($id)
    {
      $stateDataSet = Category::where('id',Crypto::decryptId($id))->update(['is_delete'=>1]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }  
    }
    
    
    public function statusupdate($id,$status){
       $categoryDataSet = Category::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($categoryDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
    
   
    
    
  
}
