<?php

namespace App\Http\Controllers\admin\leaveType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\LeaveType\LeaveTypeRepositoryInterface;
use App\Http\Requests\leaveType\LeaveTypeRequest;

class LeaveTypeController extends Controller
{
     private $leaveTypeRepo;
   function __construct(LeaveTypeRepositoryInterface $LeaveTypeRepository)
   {
     $this->leaveTypeRepo =$LeaveTypeRepository;
   }
   public function index()
   {
      $getLeaveLists =$this->leaveTypeRepo->getAll();
      return view('admin.master.leaveType',compact('getLeaveLists'));
   }
   
   public function leaveTypeSave(LeaveTypeRequest $LeaveTypeRequest)
   {
       $validated = $LeaveTypeRequest->validated();
        $getLeaveTypeSaveStatus= $this->leaveTypeRepo->create($validated);
        if($getLeaveTypeSaveStatus){
            return redirect()->back()->with("success","leavetype Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
       
       
   }
   
   public function editLeaveType($id)
    {
       
        $getLeave= $this->leaveTypeRepo->find($id);
        // $getStateLists =$this->leaveTypeRepo->getAll();
        $getLeaveLists =$this->leaveTypeRepo->getAll();
      return view('admin.master.leaveType',compact('getLeaveLists','getLeave'));
    }
    
    
     public function leaveTypeUpdate(LeaveTypeRequest $LeaveTypeRequest,$id){
         
         
        $validated = $LeaveTypeRequest->validated();
        $getleavetypeSaveStatus= $this->leaveTypeRepo->update($id,$validated);
        if($getleavetypeSaveStatus){
            return redirect('leaveType')->with("success","LeaveType update Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
    
     public function softDeleteLeaveType($id)
    {
         $delete =$this->leaveTypeRepo->delete($id);
        if($delete){
        return redirect()->back()->with("success","LeaveType Delete Successfully");
        }
              

        
    }
   
   
   
   
}
