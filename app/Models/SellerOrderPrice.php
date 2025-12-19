<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerOrderPrice extends Model
{
    protected $table="tbl_seller_order_price";
     protected $fillable = [
    'saller_id', 'staff_id', 'product_id', 'verient_id', 'price'
     ];
}
