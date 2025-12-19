<?php

namespace App\Http\Controllers\admin\city;

use App\Http\Controllers\Controller;
use App\Repositories\City\CityRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\city\CityRequest;

class CityController extends Controller
{
    private $cityRepo;
    public function __construct(CityRepositoryInterface $CityRepositoryInterface)
    {
        $this->cityRepo = $CityRepositoryInterface;
    }
    public function index()
    {
        
        $getState= $this->cityRepo->getState();
        $getCityLists =$this->cityRepo->getAll();
        return view('admin.master.city',compact('getCityLists','getState'));
    }
    
      public function citySave(CityRequest $CityRequest){
        $validated = $CityRequest->validated();
        $getCitySaveStatus= $this->cityRepo->create($validated);
        if($getCitySaveStatus){
            return redirect()->back()->with("success","City Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
    
    
    public function editCity($id)
    {
        
        
        $city =$this->cityRepo->find($id);
        $getState= $this->cityRepo->getState();
        $getCityLists =$this->cityRepo->getAll();

        return view('admin.master.city', compact('city','getState','getCityLists'));
    }
    
     public function cityUpdate(CityRequest $CityRequest,$id){
        $validated = $CityRequest->validated();
        $getCitySaveStatus= $this->cityRepo->update($id,$validated);
        if($getCitySaveStatus){
            return redirect('city')->with("success","City update Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
    
     public function softDeleteCity($id)
    {
         $delete =$this->cityRepo->delete($id);
        if($delete){
        return redirect()->back()->with("success","City Delete Successfully");
        }
              

        
    }
    

}
