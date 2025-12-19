<?php

namespace App\Http\Controllers\admin\attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Attendance\AttendanceRepositoryInterface;

class AttendanceController extends Controller
{
    
    private $attendanceRepo;
    public function __construct(AttendanceRepositoryInterface $AttendanceRepositoryInterface)
    {
        $this->attendanceRepo = $AttendanceRepositoryInterface;
    }
    public function index(Request $request)
    {
        $getAttendanceList= $this->attendanceRepo->getAll();
        $getUsers =$this->attendanceRepo->getUserAll();
        if ($request->isMethod('post')) {
            $filterType = $request->input('filter_type', 'and');
            $filters = [
                'name'         => $request->input('name'),
                'status'       => $request->input('status'),
                'date'       => $request->input('date'),
            ];
            if ($filterType === 'and') {
                if ($filters['name']) {
                    $getAttendanceList->where('user_id', $filters['name'] . '%');
                }
    
                if ($filters['status'] !== null) {
                    $getAttendanceList->where('status', $filters['status']);
                }
    
                if ($filters['date']) {
                    $getAttendanceList->whereDate('date', $filters['date']);
                }
                
            }
        }else{
            $getAttendanceList->whereDate('date', now()->toDateString());
        }
        $getAttendanceList = $getAttendanceList->with('getUser')->orderBy('id', 'desc');
        $getAttendanceList = $getAttendanceList->paginate(10);
        return view('admin.attendance.attendanceList',compact('getAttendanceList','getUsers'));
    }
    
    public function statusupdate($id,$status)
    {
       $getStaffSaveStatus= $this->attendanceRepo->statusupdate($id,$status);
        if($getStaffSaveStatus){
            return redirect()->route('stafflist')->with("success","attendance status Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
}
