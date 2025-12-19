<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\LeaveType;


class Leave extends Model
{
    //
    protected $fillable = ['staff_id','leave_id','start_date','end_date',"user_id","remark",'leave_type','date'];
    protected $table ='tbl_leaves';
    
    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
    
     public function getLeaveType(){
        return $this->hasOne(LeaveType::class,'id','leave_type');
    }
}
