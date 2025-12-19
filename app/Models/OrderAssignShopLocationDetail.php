<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAssignShopLocationDetail extends Model
{
     protected $table ='tbl_order_assign_shop_location_details';
     protected $fillable =['seller_id','staff_id','distance','rider_lat','rider_lng'];

}
