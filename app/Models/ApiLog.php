<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'log_type',
        'endpoint',
        'message',
        'data',
        'user_ip',
        'user_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];
    
    protected $table ='tbl_api_logs';
}
