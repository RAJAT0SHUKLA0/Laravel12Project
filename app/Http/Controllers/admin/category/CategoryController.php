<?php
namespace App\Http\Controllers\admin\category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Http\Requests\category\CategoryRequest;
use App\Models\Brand;

class CategoryController extends Controller
{
    
 private $CategoryRepo;
    public function __construct(CategoryRepositoryInterface $CategoryRepositoryInterface)
    {
       
        $this->CategoryRepo = $CategoryRepositoryInterface;
    }

    
    public function index(Request $request)
    {
         $brandData = Brand::where('is_delete','!=',1)->where('status',1)->get();
        $getcat = $this->CategoryRepo->getall()->with('brand');
        if ($request->isMethod('post')) {
            $filterType = $request->input('filter_type', 'and');
            $filters = [
                'name'   => $request->input('name'),
                'status' => $request->input('status'),
                'brand' => $request->input('brand_id'),
            ];
            if ($filterType === 'and') {
                if ($filters['name']) {
                    $getcat->where('tbl_category.name', 'like', '%' . $filters['name'] . '%');
                }
                 if ($filters['brand']) {
                    $getcat->where('tbl_category.brand_id', 'like', '%' . $filters['brand'] . '%');
                }
                if ($filters['status'] !== null) {
                    $getcat->where('tbl_category.status', $filters['status']);
                }
            }
        }
        $getCategoryList = $getcat->where('tbl_category.status', '!=', 3)->where('tbl_category.is_delete', '!=', 1)->paginate(10);
        return view('admin.category.categoryList', compact('getCategoryList','brandData'));
    }

    
    
     public function add()
    {
        $brandData = Brand::where('is_delete','!=',1)->where('status',1)->get();
        return view('admin.category.categoryAdd',compact('brandData'));
    }
    
    
     public function categorySave(CategoryRequest $CategoryRequest){
 
        $validated = $CategoryRequest->validated();
        $getCategorySaveStatus= $this->CategoryRepo->create($validated);
        if($getCategorySaveStatus){
            return redirect()->route('categorylist')->with("success","category Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
    }
    
    
    
     public function Edit($id)
    {
        $getcategoryInfo = $this->CategoryRepo->find($id);
         $brandData = Brand::where('is_delete','!=',1)->where('status',1)->get();
        return view('admin.category.categoryAdd',compact('getcategoryInfo','brandData'));
    }
    
     public function categoryupdate(CategoryRequest $CategoryRequest,$id){
        
        $validated = $CategoryRequest->validated();
        $getCategorySaveStatus= $this->CategoryRepo->update($id,$validated);
       
        if($getCategorySaveStatus){
            return redirect()->route('categorylist')->with("success","category update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }
    
    
     public function statusupdate($id,$status){
       $getCategorySaveStatus= $this->CategoryRepo->statusupdate($id,$status);
        if($getCategorySaveStatus){
            return redirect()->route('categorylist')->with("success","category status update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
    
    public function delete($id){
        $getCategoryDeleteStatus= $this->CategoryRepo->delete($id);
        if($getCategoryDeleteStatus){
            return redirect()->route('categorylist')->with("success","category delete Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }
    
    
    
    
}
