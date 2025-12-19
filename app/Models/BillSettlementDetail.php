<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillSettlementDetail extends Model
{
    protected $table= 'tbl_bill_settlement_details';
    protected $fillable = [
         'rider_id',
         'seller_id',
         'amount',
         'payment_mode',
         'bill_id',
     ];
     
     // Seller relationship
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id'); 
    }

    // Rider relationship
    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id'); 
    }
    
      // order relationship
    public function order()
    {
        return $this->belongsTo(Order::class, 'bill_id'); 
    }
}
