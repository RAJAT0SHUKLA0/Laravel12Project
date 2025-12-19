<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAssign extends Model
{
    protected $table ='tbl_order_assign';
    protected $fillable = ['beat_id', 'order_id', 'rider_id', 'assign_date', 'status',];
    
}
