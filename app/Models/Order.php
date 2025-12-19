<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
        protected $table ='tbl_order';
        protected $fillable = ['status', 'pickup_date','cancel_date','order_assign_date','order_rider_id'];

        public function orderDetails(){
            return  $this->hasMany(OrderDetail::class,'order_id');
        }
        
        
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
    
    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'order_id');
    }
    
   public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
      public function rider()
    {
        return $this->belongsTo(User::class, 'order_rider_id', 'id');
    }

    

}
