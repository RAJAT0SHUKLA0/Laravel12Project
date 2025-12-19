<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Regularize extends Model
{
     protected $fillable = ['staff_id','date',"user_id","remark"];
     protected $table ='tbl_regularize';
     
      public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}
