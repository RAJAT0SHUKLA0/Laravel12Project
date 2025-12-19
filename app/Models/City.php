<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
   protected $table = 'tbl_city';
    protected $fillable = [
        'name',
        'state_id',
        
    ];


 function getState(){
        return $this->hasOne(State::class,'id','state_id');
    }
    
    
    public function beats()
    {
        return $this->hasMany(Area::class, 'city_id'); // 'Area' is beat
    }
}
