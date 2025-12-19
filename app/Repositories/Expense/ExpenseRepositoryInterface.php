<?php

namespace App\Repositories\Expense;

interface ExpenseRepositoryInterface
{
    public function getAll();
    
    public function expensestatusupdate($id,$status);

   
}
