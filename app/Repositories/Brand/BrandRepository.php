<?php

namespace App\Repositories\Brand;

use App\Models\Brand;
use App\Repositories\Brand\BrandRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Utils\Uploads;
use App\Helper\Message;
use App\Services\ApiLogService;
use App\Utils\Crypto;

class BrandRepository implements BrandRepositoryInterface
{
    public function getAll()
    {
      return Brand::query();  
    }
    public function find($id)
    {
        $brandId =Crypto::decryptId($id);
       return Brand::where('id',$brandId)->first();  
    }

    public function create(array $data)
    {   try {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = Uploads::uploadImage($data['image'], 'brand', '');
            }
        $brand = Brand::create($data);
        return $brand ? true : false;
        }
        catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
    }

    public function update($id, array $data)
    {
        
        try {
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = Uploads::uploadImage($data['image'], 'brand', '');
            }
        $brand =Brand::where('id',$id)->update($data);
        return $brand ? true : false;
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
        
    }
    public function delete($id)
    {
      $brandDataSet = Brand::where('id',Crypto::decryptId($id))->update(['is_delete'=>1]); 
       if($brandDataSet){
         return true;
       }else{
         return false;
       }  
    }
    
    
    public function statusupdate($id,$status){
       $brandDataSet = Brand::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($brandDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
    
   
    
    
  
}
