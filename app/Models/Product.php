<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
        protected $fillable =['name','category_id','sub_category_id','image','hsn_code','description','brand_id'];
        protected $table ='tbl_product';
        
       
       public function getvarient(){
           return $this->belongsTo(Varient::class,'varient_id','id');
       }
        public function category(){
           return $this->hasone(Category::class,'id','category_id');
       }
       
       public function subcategory(){
           return $this->hasone(SubCategory::class,'id','sub_category_id');
       }

       public function varient(){
           return $this->hasMany(Varient::class,'id','varient_id');
       }
       
    public function getdetail(){
       return $this->hasMany(ProductDetail::class, 'product_id', 'id');
    }
    
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
    
    public function details()
    {
        return $this->hasMany(ProductDetail::class, 'product_id', 'id');
    }

    
}
