<?php

namespace App\Http\Controllers\admin\sellerType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\SellerType\SellerTypeRepositoryInterface;


use App\Http\Requests\sellerType\SellerTypeRequest;

class SellerTypeController extends Controller
{
    private $sellerTypeRepo;
   function __construct(SellerTypeRepositoryInterface $SellerTypeRepository)
   {
     $this->sellerTypeRepo =$SellerTypeRepository;
   }
   
    public function index()
   {
      $getStateLists =$this->sellerTypeRepo->getAll();
      return view('admin.master.sellerType',compact('getStateLists'));
   }
   
    public function sellerTypeSave(SellerTypeRequest $SellerTypeRequest){
        
        
        $validated = $SellerTypeRequest->validated();
        $getSellerTypeSaveStatus= $this->sellerTypeRepo->create($validated);
        if($getSellerTypeSaveStatus){
            return redirect()->back()->with("success","SellerType Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
   }
   
    public function editSellerType($id)
    {
       
        $getState= $this->sellerTypeRepo->find($id);
        $getStateLists =$this->sellerTypeRepo->getAll();
      return view('admin.master.sellerType',compact('getStateLists','getState'));
    }
    
     public function sellerTypeUpdate(SellerTypeRequest $SellerTypeRequest,$id){
         
         
        $validated = $SellerTypeRequest->validated();
        $getStateSaveStatus= $this->sellerTypeRepo->update($id,$validated);
        if($getStateSaveStatus){
            return redirect('sellerType')->with("success","sellerType update Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
   
   
    public function softDeleteSellerType($id)
    {
         $delete =$this->sellerTypeRepo->delete($id);
        if($delete){
        return redirect()->back()->with("success","SellerType Delete Successfully");
        }
    }
    
  }
