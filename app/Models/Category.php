<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
     protected $fillable = [
     'name',
     'description',
     'brand_id',
     
     ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    

    protected $table="tbl_category";

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    
     public function subcategories()
        {
            return $this->hasMany(SubCategory::class);
        }
        
         public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
    
}
