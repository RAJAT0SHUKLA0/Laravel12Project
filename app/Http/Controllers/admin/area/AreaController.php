<?php

namespace App\Http\Controllers\admin\area;

use App\Http\Controllers\Controller;
use App\Repositories\Area\AreaRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\area\AreaRequest;

class AreaController extends Controller
{
    private $AreaRepo;
    public function __construct(AreaRepositoryInterface $AreaRepositoryInterface)
    {
        $this->AreaRepo = $AreaRepositoryInterface;
    }
    
    public function index()
    {
        $getAreaLists =$this->AreaRepo->getAll();
        $getState= $this->AreaRepo->getState();
         
        
        return view('admin.master.area',compact('getAreaLists','getState'));
    }
    
     public function areaSave(AreaRequest $AreaRequest){
        $validated = $AreaRequest->validated();
        $getAreaSaveStatus= $this->AreaRepo->create($validated);
        if($getAreaSaveStatus){
            return redirect()->back()->with("success","Area Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
    
    
      public function editArea($id)
         
    {
       
        $area =$this->AreaRepo->find($id);
        $getState= $this->AreaRepo->getState();
        $getAreaLists =$this->AreaRepo->getAll();
        $getAllCity= $this->AreaRepo->getAllCity();

        return view('admin.master.area', compact('getState','area','getAreaLists','getAllCity'));
    }
    
    public function getCity(Request $request){
      $getCity= $this->AreaRepo->getCity($request->state_id);
      return response()->json(["data"=>$getCity]);
    }
    
    public function softDelete($id)
    {
         $delete =$this->AreaRepo->delete($id);
        if($delete){
        return redirect()->back()->with("success","Area Delete Successfully");
        }
              

        
    }
    
    
     public function areaUpdate(AreaRequest $AreaRequest,$id){
        $validated = $AreaRequest->validated();
        $getCitySaveStatus= $this->AreaRepo->update($id,$validated);
        if($getCitySaveStatus){
            return redirect('area')->with("success","area update Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
    
   
    
}
