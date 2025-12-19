<?php

namespace App\Http\Controllers\admin\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Staff\StaffRepositoryInterface;
use App\Http\Requests\staff\StaffRequest;
use App\Http\Requests\staff\staffChangePasswordRequest;

class StaffController extends Controller
{
   private $StaffRepo;
    public function __construct(StaffRepositoryInterface $StaffRepositoryInterface)
    {
        $this->StaffRepo = $StaffRepositoryInterface;
    }
    public function index(Request $request)
    {
        $getStaffList= $this->StaffRepo->query();
        $getState  = $this->StaffRepo->getState();
        $getAllCity= $this->StaffRepo->getAllCity();
        $getRole= $this->StaffRepo->getRole();

        
        if ($request->isMethod('post')) {
            $getAllCity= $this->StaffRepo->getCity($request->input('state_id'));
            $filterType = $request->input('filter_type', 'and');
            $filters = [
                'name'         => $request->input('name'),
                'mobile'       => $request->input('mobile'),
                'status'       => $request->input('status'),
                'staff_id'     => $request->input('staff_id'),
                'joining_date' => $request->input('joining_date'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'role_id' => $request->input('role_id'),

            ];
            if ($filterType === 'and') {
                if ($filters['name']) {
                    $getStaffList->where('name', 'like', '%' . $filters['name'] . '%');
                }
    
                if ($filters['mobile']) {
                    $getStaffList->where('mobile', 'like', '%' . $filters['mobile'] . '%');
                }
    
                if ($filters['status'] !== null) {
                    $getStaffList->where('status', $filters['status']);
                }
    
                if ($filters['staff_id']) {
                    $getStaffList->where('staff_id', $filters['staff_id']);
                }
    
                if ($filters['joining_date']) {
                    $getStaffList->whereDate('joining_date', $filters['joining_date']);
                }
                 if ($filters['state_id']) {
                    $getStaffList->where('state_id', $filters['state_id']);
                }
                 if ($filters['city_id']) {
                    $getStaffList->where('city_id', $filters['city_id']);
                }
                 if ($filters['role_id']) {
                    $getStaffList->where('role_id', $filters['role_id']);
                }
            }
        }
        $getStaffList =$getStaffList->with(['role','state','city'])->where('status','!=',3);
        $getStaffList1 = $getStaffList->orderBy('id','desc')->paginate(10)->appends($request->query());
        return view('admin.staff.staffList',compact('getStaffList1','getState','getAllCity','getRole'));
    }
    
    public function add()
    {
        $getState= $this->StaffRepo->getState();
        $getRole= $this->StaffRepo->getRole();
        return view('admin.staff.staffAdd',compact('getState','getRole'));
    }

    public function Edit($id)
    {
        $getStaffInfo = $this->StaffRepo->find($id);
        $getState= $this->StaffRepo->getState();
        $getAllCity= $this->StaffRepo->getAllCity();
        $getRole= $this->StaffRepo->getRole();
        return view('admin.staff.staffAdd',compact('getState','getRole','getAllCity','getStaffInfo'));
    }
    
    public function staffSave(StaffRequest $StaffRequest){
        $validated = $StaffRequest->validated();
        $getStaffSaveStatus= $this->StaffRepo->create($validated);
        if($getStaffSaveStatus){
            return redirect()->back()->with("success","staff Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
    }

    public function staffupdate(StaffRequest $StaffRequest,$id){
        $validated = $StaffRequest->validated();
        $getStaffSaveStatus= $this->StaffRepo->update($id,$validated);
        if($getStaffSaveStatus){
            return redirect()->route('stafflist')->with("success","staff update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }

    public function delete($id){
        $getStaffSaveStatus= $this->StaffRepo->delete($id);
        if($getStaffSaveStatus){
            return redirect()->route('stafflist')->with("success","staff delete Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }
    
    public function statusupdate($id,$status){
       $getStaffSaveStatus= $this->StaffRepo->statusupdate($id,$status);
        if($getStaffSaveStatus){
            return redirect()->route('stafflist')->with("success","staff status Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
    
    public function changePassword(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            return view('admin.staff.staffChangePassword');
        }
    
        $validated = app(staffChangePasswordRequest::class);
    
        $getStaffSavePassword = $this->StaffRepo->changePassword($id, $validated->password);
    
        if ($getStaffSavePassword) {
            return redirect()->route('stafflist')->with("success", "Staff password changed successfully");
        } else {
            return redirect()->back()->with("error", "Something went wrong");
        }
    }

    
    
    public function getCity(Request $request){
      $getCity= $this->StaffRepo->getCity($request->state_id);
      return response()->json(["data"=>$getCity]);
    }
    
    
    public function isLocationEnable($id,$status){
        $getCity= $this->StaffRepo->isLocationEnable($id,$status);
        return response()->json(["status"=>true,'msg'=>"successfully Location enable "]);
    }
}
