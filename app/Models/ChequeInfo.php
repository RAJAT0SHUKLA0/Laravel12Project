<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChequeInfo extends Model
{
    protected $table = 'tbl_cheque_info';
    protected $fillable = [
        'seller_id',
        'staff_id',
        'date',
        'cheque_clear_date',
        'image',
        'amount',
        'type',
        'deduct_amount',
        'is_already_submitted',
    ];
    
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
}
