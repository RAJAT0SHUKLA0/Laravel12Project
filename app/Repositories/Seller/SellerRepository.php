<?php

namespace App\Repositories\Seller;

use App\Models\City;
use App\Models\State;
use App\Models\Seller;
use App\Models\Area;
use App\Models\User;
use App\Repositories\Seller\SellerRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Utils\Uploads;
use App\Helper\Message;
use App\Services\ApiLogService;
use App\Utils\Crypto;
use App\Models\TransactionHistory;


class SellerRepository implements SellerRepositoryInterface
{
    public function getAll()
    {
        
      return Seller::query();  
    }
    public function find($id)
    {
        $sellerId =Crypto::decryptId($id);
      return Seller::where('id',$sellerId)->first();  
    }

    public function create(array $data)
    {   try {
       
       
        
         $seller = Seller::orderby('id','desc')->whereNotNull('seller_id')->first();
        if (!empty($seller)) {
            $lastStaffId = ltrim($seller->seller_id, '0'); 
            $exploadData=explode('-',$lastStaffId);
            $lastStaffId = is_string($lastStaffId) ? (int)$exploadData[1] : 0;
            $nextStaffId = str_pad($lastStaffId + 1, 3, '0', STR_PAD_LEFT);
            $data['seller_id'] = $exploadData[0].'-'.$nextStaffId;
        }

        if (isset($data['profile_pic']) && $data['profile_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['profile_pic'] = Uploads::uploadImage($data['profile_pic'], 'profile', 'profile_pic');
        }
        if (isset($data['addhar_front_pic']) && $data['addhar_front_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['addhar_front_pic'] = Uploads::uploadImage($data['addhar_front_pic'], 'aadhar', 'aadhar_front');
        }
        if (isset($data['addhar_back_pic']) && $data['addhar_back_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['addhar_back_pic'] = Uploads::uploadImage($data['addhar_back_pic'], 'aadhar', 'aadhar_back');
        }
        
       
        $seller = Seller::create($data);
        return $seller ? true : false;
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
    }

    public function update($id, array $data)
    {
        
        try {
        if (isset($data['profile_pic']) && $data['profile_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['profile_pic'] = Uploads::uploadImage($data['profile_pic'], 'profile', 'profile_pic');
        }
        if (isset($data['addhar_front_pic']) && $data['addhar_front_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['addhar_front_pic'] = Uploads::uploadImage($data['addhar_front_pic'], 'aadhar', 'aadhar_front');
        }
        if (isset($data['addhar_back_pic']) && $data['addhar_back_pic'] instanceof \Illuminate\Http\UploadedFile) {
            $data['addhar_back_pic'] = Uploads::uploadImage($data['addhar_back_pic'], 'aadhar', 'aadhar_back');
        }
    
        
        $user =Seller::where('id',$id)->update($data);
        return $user ? true : false;
        }catch (\Exception $e) {
            ApiLogService::error(Message::SERVER_ERROR_MESSAGE, $e);
            return false;
        }
        
    }
    public function delete($id)
    {
       
      $stateDataSet = Seller::where('id',Crypto::decryptId($id))->update(['status'=>3]); 
      if($stateDataSet){
         return true;
      }else{
         return false;
      }  
    }
    
    
    public function statusupdate($id,$status){
      $stateDataSet = Seller::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
      if($stateDataSet){
         return true;
      }else{
         return false;
      }   
    }
    
    
   
    public function getState()
    {
      return  State::get();
    }
    
    
    public function getCity($state_id)
    {
      return City::where('state_id',$state_id)->get();
    }
   

    public function getAllCity(){
        return City::get();
    }
    
    
    public function getArea($city_id){
     return Area ::where('city_id',$city_id)->get();
        }
        
    public function getAllArea(){
        return Area::get();
    }
    
    
    public function getAllSellerData($id){
        return Seller::with(['order' => function ($query) {$query->latest()->take(5);},'transaction'=> function ($query) {$query->where('status','!=','2');},'order.orderDetails','order.staff:id,name','order.orderDetails.product:id,name,image,description','order.orderDetails.variant:id,name','order.orderDetails.productDetail:product_id,varient_id,retailer_price,gst'])->withCount('order') ->where('id',Crypto::decryptId($id))->first();
    }
    
     public function getTransactionReport($sellerId){
        return TransactionHistory::with(['seller','staff','transaction'])->where('seller_id',Crypto::decryptId($sellerId))->paginate(10);
     }
        
}
