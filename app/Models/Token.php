<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'tbl_token';
    protected $fillable = [
        'token',
        'user_id'
    ];
}
