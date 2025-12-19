<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Models\Varient;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductDetail;

use App\Models\SubCategory;
use App\Utils\Uploads;
use App\Helper\Message;
use App\Services\ApiLogService;
use App\Utils\Crypto;
use App\Repositories\Product\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function query()
    {
      return Product::query();  
    }
    public function find($id)
    {
        $staffId =Crypto::decryptId($id);
       return Product::where('id',$staffId)->first();  
    }

    public function create(array $data)
    {   try {
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = Uploads::uploadImage($data['image'], 'product', '');
            }
            $array =[];
            $Product = Product::create($data);
            for($i=0; $i < count($data['varient_id']);$i++ ){
                $array[]= array('product_id'=>$Product->id,'varient_id'=>$data['varient_id'][$i],'mrp'=>$data['mrp'][$i],'retailer_price'=>$data['retailer_price'][$i],'gst'=>$data['gst'][$i]);
            }
            $ProductDetail = ProductDetail::insert($array);
            return $Product && $ProductDetail ? true : false;
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
    }

    public function update($id, array $data)
    {
        try {
            
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = Uploads::uploadImage($data['image'], 'product', '');
            }
            $Product =Product::where('id',$id)->first();
            $datas = [
                'name'            => $data['name'],
                'category_id'     => $data['category_id'],
                'sub_category_id' => $data['sub_category_id']??null,
                'image'           =>  $data['image']??$Product->image,
                'hsn_code'        => $data['hsn_code'],
                'description'     => $data['description'],
                'brand_id'        => $data['brand_id'],
            ];

            $Productstatus =$Product->update($datas);
                $detailsUpdated = true;
                for ($i = 0; $i < count($data['varient_id']); $i++) {
                    $detailData = [
                        'product_id'     => $id,
                        'varient_id'     => $data['varient_id'][$i],
                        'mrp'            => $data['mrp'][$i],
                        'retailer_price' => $data['retailer_price'][$i],
                        'gst'            => $data['gst'][$i], // Confirm if this is intended
                        'updated_at'     => now(),
                    ];
        
                   if (isset($data['node_id'][$i]) && !empty($data['node_id'][$i])) {
                        $updated = ProductDetail::where('id', $data['node_id'][$i])->update($detailData);
                        $detailsUpdated = $detailsUpdated && $updated;
                    } else {
                        $ProductDetail = ProductDetail::insert($detailData);
                        $detailsUpdated = true;
                    }
                }
                
            return $Product && $detailsUpdated ? true : false;
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
        
    }
    public function delete($id)
    {
      $ProductDataSet = Product::where('id',Crypto::decryptId($id))->update(['status'=>3]); 
       if($ProductDataSet){
         return true;
       }else{
         return false;
       }  
    }
    
    
    public function statusupdate($id,$status){
       $ProductDataSet = Product::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($ProductDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
    public function getCategory(){
       return Category::where('status',1)->get(); 
       
    }
    
    public function getVarient(){
       return Varient::where('status',1)->get(); 
       
    }
    public function getSubCategory(){
       return SubCategory::where('status',1)->get(); 
       
    }
    
    public function checkSubcategory($id){
       return SubCategory::where('category_id',$id)->where('status',1)->get(); 
    }
    
    
    public function getDetails($id){
      return ProductDetail::where('product_id',Crypto::decryptId($id))->get();   
    }
    
    public function getAllbrand(){
       return Brand::where('status',1)->get(); 
    }
  
    
}
