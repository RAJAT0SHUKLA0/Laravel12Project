<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerType extends Model
{
    protected $fillable =['name'];
    protected $table="tbl_sellerType";
    
    
        public function cities()
        {
            return $this->hasMany(City::class);
        }
}
