<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Varient extends Model
{
    protected $table = 'tbl_varient';

    protected $fillable = [
        'name',
        'unit_id',
    ];

    public function unit(){
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . ($this->unit->name ?? '');
    }
}

