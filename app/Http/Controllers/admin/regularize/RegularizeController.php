<?php

namespace App\Http\Controllers\admin\regularize;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Regularize\RegularizeRepositoryInterface;



class RegularizeController extends Controller
{
     private $regularizeRepo;
   function __construct(RegularizeRepositoryInterface $RegularizeRepository)
   {
     $this->regularizeRepo =$RegularizeRepository;
   }
   
    public function index(Request $request)
   {
      $getleaveLists =$this->regularizeRepo->getAll();
      
      
      if ($request->isMethod('post')) {
            
            $filterType = $request->input('filter_type', 'and');
            $filters = [
                
                
                'date'       => $request->input('date'),
                
                'status'       => $request->input('status'),
               

            ];
            if ($filterType === 'and') {
               
                
    
                if ($filters['status'] !== null) {
                    $getleaveLists->where('status', $filters['status']);
                }
    
                if ($filters['date']) {
                    $getleaveLists->where('date', $filters['date']);
                }
    
               
               
            }
        }
        
        $getleaveLists = $getleaveLists->orderby('id','desc')->paginate(10)->appends($request->query());
       return view('admin.regularize.regularizeList',compact('getleaveLists'));
   }
   
    public function Regularizestatusupdate($id,$status){
       $getRegularizeSaveStatus= $this->regularizeRepo->Regularizestatusupdate($id,$status);
        if($getRegularizeSaveStatus){
            return redirect()->route('regularizelist')->with("success","regularize status Update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
   
}
