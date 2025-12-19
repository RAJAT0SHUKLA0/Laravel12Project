<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
            protected $table ='tbl_order_details';
            
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    public function variant()
    {
        return $this->belongsTo(Varient::class, 'varient_id');
    }
    
    public function productDetail()
    {
        return $this->hasOne(ProductDetail::class, 'product_id', 'product_id');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    
   

}
