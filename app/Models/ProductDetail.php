<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    //
    protected $fillable =['product_id','varient_id','mrp','retailer_price','gst'];
    protected $table ='tbl_product_details';
    
    public function varient(){
       return $this->hasOne(Varient::class,'id','varient_id');
    }
      public function OrderAssign()
    {
        return $this->hasOne(OrderAssign::class, 'order_detail_id', 'id');
    }
}
