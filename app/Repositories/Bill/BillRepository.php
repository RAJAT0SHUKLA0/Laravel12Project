<?php

namespace App\Repositories\Bill;

use App\Repositories\Bill\BillRepositoryInterface;
use App\Models\ChequeInfo;
use App\Models\Seller;
use App\Utils\Uploads;
use App\Utils\Crypto;

class BillRepository implements BillRepositoryInterface
{
    public function getAll()
    {
      return ChequeInfo::orderBy('id','desc')->paginate(10);
    }
    public function find($id)
    {
       return ChequeInfo::where('id',$id)->first();  
    }

    public function create(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = Uploads::uploadImage($data['image'], 'bill', 'bill');
        }
      $data['date'] = now()->format('Y-m-d');
        $getPayment->deduct_amount  =  $request->amount;
        $data['deduct_amount'] = $data['amount'];
       $stateDataSet = ChequeInfo::create($data);
       
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
    }
    public function update($id, array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = Uploads::uploadImage($data['image'], 'bill', 'bill');
        }
       $stateDataSet = ChequeInfo::where('id',$id)->update($data); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    public function status($id,$status){
       $stateDataSet = ChequeInfo::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
    
    public function getSeller(){
        return Seller::where('status',1)->get();
    }
}
