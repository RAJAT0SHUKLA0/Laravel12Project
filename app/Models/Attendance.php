<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Attendance extends Model
{
     protected $fillable = [
     'in_time',
     'out_time',
     "date",
     "user_id",
     "working_hours",
     'in_time_status',
     "out_time_status",
     "in_time_lat",
     "in_time_long",
     "in_time_address",
     "out_time_lat",
     "out_time_long",
     "out_time_address",

     ];

   protected $table = 'tbl_attentance';
   
   public function getUser(){
      
      return $this->hasOne(User::class,'id','user_id');
       
   }
}
