<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use App\Models\State;
use App\Models\City;

class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
     protected $fillable = [
     'name',
     'email',
     "mobile",
     "role_id",
     "joining_date",
     "profile_pic",
     "addhar_front_pic",
     "addhar_back_pic",
     "state_id",
     "city_id",
      'password',
      'staff_id',
      'device_id'
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

    protected $table="tbl_users";

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
            'role_id' => 'string',
        ];
    }
    
    public function role(){
        return $this->hasOne(Role::class,'id','role_id');
    }
    public function state(){
        return $this->hasOne(State::class,'id','state_id');
    }
    public function city(){
        return $this->hasOne(City::class,'id','city_id');
    }
}
