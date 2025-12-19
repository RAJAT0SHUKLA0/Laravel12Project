<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\State;
use App\Models\City;
use App\Models\Area;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
   

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
     protected $fillable = [
    'seller_id',
     'name',
     'shop_name',
     'email',
     "mobile",
     "whatsapp_no",
     "profile_pic",
     "addhar_front_pic",
     "addhar_back_pic",
     "state_id",
     "city_id",
     "sellertype_id",
      "latitude",
      "longitude",
      "address",
      "beat_id"
     
     
     ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $table="tbl_sellers";

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            
        ];
    }
    
    
    public function state(){
        return $this->hasOne(State::class,'id','state_id');
    }
    public function city(){
        return $this->hasOne(City::class,'id','city_id');
    }
    public function area(){
        return $this->hasOne(Area::class,'id','beat_id');
    }
    
     public function order(){
        return $this->hasMany(Order::class,'seller_id','id');
    }
    
     public function transaction()
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }
    
    
    
    public function chequeInfos()
    {
        return $this->hasMany(ChequeInfo::class, 'seller_id');
    }

}
