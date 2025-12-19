<?php

namespace App\Http\Controllers\admin\expense;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Expense\ExpenseRepositoryInterface;

class ExpenseController extends Controller
{
     private $expenseRepo;
   function __construct(ExpenseRepositoryInterface $ExpenseRepository)
   {
     $this->expenseRepo =$ExpenseRepository;
   }
   
    public function index(Request $request)
   {
     
      $getexpenseLists =$this->expenseRepo->getAll();
       if ($request->isMethod('post')) {
           
            $filterType = $request->input('filter_type', 'and');
            $filters = [
                'expense_date'       => $request->input('expense_date'),
                'status'       => $request->input('status'),
            ];
            if ($filterType === 'and') {
             
                if ($filters['status'] !== null) {
                    $getexpenseLists->where('status', $filters['status']);
                }
    
                if ($filters['expense_date']) {
                    $getexpenseLists->where('expense_date', $filters['expense_date']);
                }
            }
        }
        
        $getexpenseLists = $getexpenseLists->orderBy('id', 'desc')->paginate(10)->appends($request->query());
       return view('admin.expense.expenseList',compact('getexpenseLists'));
   }
   
    public function expensestatusupdate($id,$status){
       $getExpenseSaveStatus= $this->expenseRepo->expensestatusupdate($id,$status);
        if($getExpenseSaveStatus){
            return redirect()->route('expenselist')->with("success","expense status Update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
   
}
