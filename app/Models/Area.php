<?php

namespace App\Models;
use App\Models\State;
use App\Models\City;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
     protected $table = 'tbl_area';
      protected $fillable = [
        'name',
        'state_id',
        'city_id',
    ];


 function getState(){
        return $this->hasOne(State::class,'id','state_id');
    }
    
    function getCity(){
        return $this->hasOne(City::class,'id','city_id');
    }
}
