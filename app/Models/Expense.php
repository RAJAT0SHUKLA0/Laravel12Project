<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Expense extends Model
{
    protected $fillable = ['staff_id','expense_date','expense_amount','expense_image','status','remark'];
    protected $table ='tbl_expenses';
    
    public function user(){
        return $this->hasOne(User::class,'id','staff_id');
    }

}
