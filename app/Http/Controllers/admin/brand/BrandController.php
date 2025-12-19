<?php

namespace App\Http\Controllers\admin\brand;

use App\Repositories\Brand\BrandRepositoryInterface;
use App\Http\Requests\brand\BrandRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    private $BrandRepo;
    public function __construct(BrandRepositoryInterface $BrandRepositoryInterface)
    {
       
        $this->BrandRepo = $BrandRepositoryInterface;
    }
    
     public function index(Request $Request)
    {
        
       
    $getbrand= $this->BrandRepo->getall();
    $getBrandList =$getbrand->where('is_delete','!=',1)->paginate(10);
         
       return view('admin.brand.brandList',compact('getBrandList'));
    }
    
    
     public function add()
    {
       
    return view('admin.brand.brandAdd');
    }
    
     public function brandSave(BrandRequest $BrandRequest)
     {
        
         $validated = $BrandRequest->validated();
        $getBrandSaveStatus= $this->BrandRepo->create($validated);
        if($getBrandSaveStatus){
            return redirect()->route('brandlist')->with("success","brand Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
    }
    
    
    
     public function Edit($id)
    {
        $getbrandInfo = $this->BrandRepo->find($id);
       
        return view('admin.brand.brandAdd',compact('getbrandInfo'));
    }
    
    
    
     public function brandupdate(BrandRequest $BrandRequest,$id){
        
        $validated = $BrandRequest->validated();
        $getBrandSaveStatus= $this->BrandRepo->update($id,$validated);
       
        if($getBrandSaveStatus){
            return redirect()->route('brandlist')->with("success","brand update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }
    
    
    
     public function statusupdate($id,$status){
       $getBrandSaveStatus= $this->BrandRepo->statusupdate($id,$status);
        if($getBrandSaveStatus){
            return redirect()->route('brandlist')->with("success","brand status update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
    
    public function delete($id){
        $getBrandDeleteStatus= $this->BrandRepo->delete($id);
        if($getBrandDeleteStatus){
            return redirect()->route('brandlist')->with("success","brand delete Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }
}
