<?php

namespace App\Http\Controllers\admin\state;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\State\StateRepositoryInterface;
use App\Http\Requests\state\StateRequest;

class StateController extends Controller
{
  private $stateRepo;
   function __construct(StateRepositoryInterface $StateRepository)
   {
     $this->stateRepo =$StateRepository;
   }
   public function index()
   {
      $getStateLists =$this->stateRepo->getAll();
      return view('admin.master.state',compact('getStateLists'));
   }
   
    public function stateSave(StateRequest $StateRequest){
        
        
        $validated = $StateRequest->validated();
        $getStateSaveStatus= $this->stateRepo->create($validated);
        if($getStateSaveStatus){
            return redirect()->back()->with("success","State Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
   }
   
    public function editState($id)
    {
       
        $getState= $this->stateRepo->find($id);
        $getStateLists =$this->stateRepo->getAll();
      return view('admin.master.state',compact('getStateLists','getState'));
    }
    
     public function stateUpdate(StateRequest $StateRequest,$id){
         
         
        $validated = $StateRequest->validated();
        $getStateSaveStatus= $this->stateRepo->update($id,$validated);
        if($getStateSaveStatus){
            return redirect('state')->with("success","state update Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
   
   
    public function softDeleteState($id)
    {
         $delete =$this->stateRepo->delete($id);
        if($delete){
        return redirect()->back()->with("success","State Delete Successfully");
        }
              

        
    }
   
   
   
   
   
}