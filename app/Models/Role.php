<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
   use HasFactory;
   protected $table="tbl_role";
    //
    
    public function permissions() {
    return $this->hasMany(permission::class);
}
}
