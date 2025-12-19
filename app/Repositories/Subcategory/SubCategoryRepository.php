<?php

namespace App\Repositories\Subcategory;
use App\Models\SubCategory;
use App\Models\Category;
use App\Utils\Crypto;
use App\Repositories\Subcategory\SubCategoryRepositoryInterface;
class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    public function getAll()
    {
      return SubCategory::query();  
    }
    public function find($id)
    {
       $Subcategoryid =Crypto::decryptId($id);
       return SubCategory::where('id',$Subcategoryid)->first(); 
       
    }

    public function create(array $data)
    {
       $stateDataSet = SubCategory::create($data);
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
    }
    public function update($id, array $data)
    {
       $subcategoryDataSet = SubCategory::where('id',$id)->update($data); 
       if($subcategoryDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    
      public function statusupdate($id,$status){
       $stateDataSet = SubCategory::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
    
    
    
    public function delete($id)
    {
      $subcategoryDataSet = SubCategory::where('id',Crypto::decryptId($id))->update(['is_delete'=>1]); 
       if($subcategoryDataSet){
         return true;
       }else{
         return false;
       }  
    }
    
      public function getCategory()
    {
        return Category::get();
    }

}
