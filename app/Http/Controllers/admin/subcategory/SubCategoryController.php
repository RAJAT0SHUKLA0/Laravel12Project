<?php

namespace App\Http\Controllers\admin\subcategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Subcategory\SubCategoryRepositoryInterface;
use App\Http\Requests\subcategory\SubCategoryRequest;

class SubCategoryController extends Controller
{
    private $subcategoryRepo;
    public function __construct(SubCategoryRepositoryInterface $SubCategoryRepositoryInterface)
    {
        $this->subcategoryRepo = $SubCategoryRepositoryInterface;
    }
    public function index(Request $request)
    {
        
        $getState= $this->subcategoryRepo->getCategory();
        $getSubcategoryLists =$this->subcategoryRepo->getAll();
        
         if ($request->isMethod('post')) {
            
            $filterType = $request->input('filter_type', 'and');
            $filters = [
                'name'         => $request->input('name'),
                
                'status'       => $request->input('status'),
                'category_id'     => $request->input('category_id'),
                
                

            ];
            if ($filterType === 'and') {
                if ($filters['name']) {
                    $getSubcategoryLists->where('name', 'like', '%' . $filters['name'] . '%');
                }
    
               
    
                if ($filters['status'] !== null) {
                    $getSubcategoryLists->where('status', $filters['status']);
                }
    
                if ($filters['category_id']) {
                    $getSubcategoryLists->where('category_id', $filters['category_id']);
                }
    
               
               
                
            }
        }
        
         $getCityLists =$getSubcategoryLists->where('status','!=',3)->paginate(10);
       
        
        return view('admin.subcategory.subcategoryList',compact('getCityLists','getState'));
    }
    
     public function add()
    {
        $getState= $this->subcategoryRepo->getCategory();
        return view('admin.subcategory.subcategoryAdd',compact('getState'));
    }
    
      public function subcategorySave(SubCategoryRequest $SubCategoryRequest){
        $validated = $SubCategoryRequest->validated();
        $getsubcategorySaveStatus= $this->subcategoryRepo->create($validated);
        if($getsubcategorySaveStatus){
            return redirect()->route('subcategorylist')->with("success","Subcategory Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
    
    
    public function editSubcategory($id)
    {
        
        $city =$this->subcategoryRepo->find($id);
       
        
       $getState= $this->subcategoryRepo->getCategory();
        $getCityLists =$this->subcategoryRepo->getAll();

        return view('admin.subcategory.subcategoryAdd', compact('city','getState','getCityLists'));
    }
    
     public function SubcategoryUpdate(SubCategoryRequest $SubCategoryRequest,$id){
         
         
         
        $validated = $SubCategoryRequest->validated();
        $getSubcategorySaveStatus= $this->subcategoryRepo->update($id,$validated);
        if($getSubcategorySaveStatus){
            return redirect()->route('subcategorylist')->with("success","subcategory update Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
        
    }
    
    
     public function statusupdate($id,$status){
       $getStaffSaveStatus= $this->subcategoryRepo->statusupdate($id,$status);
        if($getStaffSaveStatus){
            return redirect()->route('subcategorylist')->with("success","subcategory status update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
    
     public function softDeleteSubcategory($id)
    {
         $delete =$this->subcategoryRepo->delete($id);
        if($delete){
        return redirect()->back()->with("success","subcategory Delete Successfully");
        }
              

        
    }
    
}
