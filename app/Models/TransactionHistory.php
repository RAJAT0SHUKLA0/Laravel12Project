<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    protected $table = 'tbl_transaction_history';
    protected $fillable = ['bill_id', 'date', 'deduct_amount', 'payment_mode','seller_id', 'staff_id'];
    
    public function seller(){
        return $this->belongsTo(Seller::class,'seller_id');
    }
    public function staff(){
        return $this->belongsTo(User::class,'staff_id');
    }
    
     public function transaction(){
        return $this->belongsTo(Transaction::class,'bill_id');
    }
    
}
