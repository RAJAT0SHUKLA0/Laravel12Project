<?php

namespace App\Http\Controllers\admin\varient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Varient\VarientRepositoryInterface;
use App\Http\Requests\varient\VarientRequest;

class VarientController extends Controller
{
     private $varientRepo;
    public function __construct(VarientRepositoryInterface $VarientRepositoryInterface)
    {
        $this->varientRepo = $VarientRepositoryInterface;
    }
    
    
    public function index()
    {
        
        $getUnit= $this->varientRepo->getUnit();
        $getVarientList =$this->varientRepo->getAll();
        return view('admin.varient.varient',compact('getVarientList','getUnit'));
    }
    
    
     public function varientSave(VarientRequest $VarientRequest){
        $validated = $VarientRequest->validated();
        $getvarientSaveStatus= $this->varientRepo->create($validated);
        if($getvarientSaveStatus){
            return redirect()->back()->with("success","varient Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
    
     public function editvarient($id)
    {
        
        $varient =$this->varientRepo->find($id);
       
       
        
       $getUnit= $this->varientRepo->getUnit();
       
        $getVarientList =$this->varientRepo->getAll();

        return view('admin.varient.varient', compact('varient','getUnit','getVarientList'));
    }
    
    public function VarientUpdate(VarientRequest $VarientRequest,$id){
         
         
         
        $validated = $VarientRequest->validated();
        $getSubcategorySaveStatus= $this->varientRepo->update($id,$validated);
        if($getSubcategorySaveStatus){
            return redirect('varient')->with("success","varient update Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
    
     public function softDeleteVarient($id){
        $getVarientSaveStatus= $this->varientRepo->delete($id);
        if($getVarientSaveStatus){
            return redirect()->route('varientlist')->with("success","varient delete Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }
    
     public function statusupdate($id,$status){
       $getVarientStatus= $this->varientRepo->statusupdate($id,$status);
        if($getVarientStatus){
            return redirect()->route('varientlist')->with("success","varient update status Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
    
    
   
}
