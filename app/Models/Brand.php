<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    
    
     protected $fillable = [
     'name',
    'image',
    'status',
     
     
     ];
       protected $table="tbl_brands";
       
       public function categories()
    {
        return $this->hasMany(Category::class, 'brand_id', 'id');
    }
}
