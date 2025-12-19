<?php

namespace App\Http\Controllers\admin\leave;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Leave\LeaveRepositoryInterface;



class LeaveController extends Controller
{
     private $leaveRepo;
   function __construct(LeaveRepositoryInterface $LeaveRepository)
   {
     $this->leaveRepo =$LeaveRepository;
   }
   
    public function index(Request $request)
   {
      $getleaveLists =$this->leaveRepo->getAll();
      
      
       if ($request->isMethod('post')) {
            
            $filterType = $request->input('filter_type', 'and');
            $filters = [
                
                
                'start_date'       => $request->input('start_date'),
                'end_date'     => $request->input('end_date'),
                'status'       => $request->input('status'),
               

            ];
            if ($filterType === 'and') {
               
                
    
                if ($filters['status'] !== null) {
                    $getleaveLists->where('status', $filters['status']);
                }
    
                if ($filters['start_date']) {
                    $getleaveLists->where('start_date', $filters['start_date']);
                }
    
                if ($filters['end_date']) {
                    $getleaveLists->whereDate('end_date', $filters['end_date']);
                }
               
            }
        }
        
        $getleaveLists = $getleaveLists->orderBy('id', 'desc')->paginate(10)->appends($request->query());
        
       return view('admin.leave.leaveList',compact('getleaveLists'));
   }
   
    public function leavestatusupdate($id,$status){
       $getLeaveSaveStatus= $this->leaveRepo->leavestatusupdate($id,$status);
        if($getLeaveSaveStatus){
            return redirect()->route('leavelist')->with("success","leave status Update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
   
}
