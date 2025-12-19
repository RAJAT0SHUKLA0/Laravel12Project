<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['user_id','latitude','longitude','date','address','battery_level','accuracy_meter','accuracy_status'];
    protected $table = 'tbl_location';
    public function getUser(){
      
      return $this->hasOne(User::class,'id','user_id');
       
   }
}
