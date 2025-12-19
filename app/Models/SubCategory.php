<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
     'name',
     'category_id',
     'description',
     
     
     ];
     
     protected $table="tbl_subcategory"; 
     
     function getCategory(){
        return $this->hasOne(Category::class,'id','category_id');
    }
}
