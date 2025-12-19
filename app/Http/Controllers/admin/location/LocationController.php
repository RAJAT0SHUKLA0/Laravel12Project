<?php

namespace App\Http\Controllers\admin\location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\Location\LocationRepositoryInterface;
use App\Models\Location;
class LocationController extends Controller
{
    
    private $locationRepo;
    public function __construct(LocationRepositoryInterface $LocationRepositoryInterface)
    {
        $this->locationRepo = $LocationRepositoryInterface;
    }
    public function index(Request $request){
        $getAttendanceList= $this->locationRepo->getAll();
        $getUsers =$this->locationRepo->getUserAll();
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
        }
        $getAttendanceList= $getAttendanceList->whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('tbl_location')
                ->groupBy('user_id');
        })
        ->with('getUser')
        ->orderByDesc('date')
        ->paginate(10);
        return view('admin.location.locationList',compact('getAttendanceList','getUsers'));
    }
    
    public function renderLocationList(Request $request)
    {
        $locationlist = Location::where("user_id",$request->user_id)->orderBy('id','desc')->paginate(10);
        $html = view('components.location-modal', compact('locationlist'))->render();
        return response()->json(['html' => $html]);
    }
    
}
