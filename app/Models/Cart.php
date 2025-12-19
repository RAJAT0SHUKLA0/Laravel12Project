<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{Product,Varient,ProductDetail,User};
class Cart extends Model
{
    protected $table="tbl_cart";
    
    // Cart.php

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
    
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }


}
