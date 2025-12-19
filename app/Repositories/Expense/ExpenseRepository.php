<?php

namespace App\Repositories\Expense;

use App\Repositories\Expense\ExpenseRepositoryInterface;
use App\Models\Expense;
use App\Models\User;
use App\Utils\Crypto;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function getAll()
    {
      return Expense::query();  
    }
     public function expensestatusupdate($id,$status){
       $stateDataSet = Expense::where('id',Crypto::decryptId($id))->update(['status'=>$status]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }   
    }
    
   
    
}
