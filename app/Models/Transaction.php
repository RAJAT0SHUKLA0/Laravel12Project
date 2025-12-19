<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'tbl_transaction';
    public function histories()
    {
        return $this->hasMany(TransactionHistory::class, 'bill_id');
    }
    
    public function seller()
{
    return $this->belongsTo(Seller::class, 'seller_id', 'id');
}

public function staff()
{
    return $this->belongsTo(User::class, 'staff_id', 'id');
}


 public function order()
    {
        return $this->belongsTo(Order::class, 'order_id'); 
    }

}
