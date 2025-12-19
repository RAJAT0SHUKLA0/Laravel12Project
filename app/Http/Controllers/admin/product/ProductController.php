<?php

namespace App\Http\Controllers\admin\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\product\ProductRequest;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductDetail;
use App\Utils\Crypto;

class ProductController extends Controller
{
    private $productRepo;
    public function __construct(ProductRepositoryInterface $ProductRepositoryInterface)
    {
        $this->productRepo = $ProductRepositoryInterface;
    }
    public function index(Request $request){
    //   dd($request->all());
        $getProducts = $this->productRepo->query()
    ->with(['brand', 'details.varient.unit']);
         $getCategory =$this->productRepo->getCategory();
         $brands = Brand::where('status',1)->where('is_delete',0)->get();
         $getVarient =$this->productRepo->getVarient();
         
        if ($request->isMethod('post')) {
            $getAllCity= $this->productRepo->checkSubcategory($request->input('category_id'));
            $filterType = $request->input('filter_type', 'and');
            $filters = [
                'name'         => $request->input('name'),
                'category_id'       => $request->input('category_id'),
                'brand_id'       => $request->input('brand_id'),
                'varient_id'       => $request->input('varient_id'),
                'status'       => $request->input('status'),
            ];
            if ($filterType === 'and') {
                if ($filters['name']) {
                    $getProducts->where('name', 'like', '%' . $filters['name'] . '%');
                }
    
                if ($filters['category_id']) {
                    $getProducts->where('category_id',$filters['category_id']);
                }
    
                if ($filters['status'] !== null) {
                    $getProducts->where('status', $filters['status']);
                }
    
                if ($filters['brand_id']) {
                    $getProducts->where('brand_id', $filters['brand_id']);
                }
                if ($filters['varient_id']) {
                    $getProducts->whereHas('details', function ($q) use ($filters) {
                        $q->where('varient_id', $filters['varient_id']);
                    });
                }
            }
        }
        
        $getProductsList= $getProducts->orderBy('id','desc')->where('status','!=','3')->paginate(10);
         return view('admin.product.productList',compact('getProductsList','getVarient','getCategory','brands'));  
    }
    
    public function add(){
        $getCategory =$this->productRepo->getCategory();
        $getVarient =$this->productRepo->getVarient();
        $getAllbrand = $this->productRepo->getAllbrand();
        return view('admin.product.productAdd',compact('getCategory','getVarient','getAllbrand'));   
    }
    public function save(ProductRequest $request){
    //   dd($request->all());
       $validated = $request->validated();
       $category = Category::find($validated['category_id']);
        if ($category && $category->brand_id) {
            $validated['brand_id'] = $category->brand_id;
        }
       $newProduct =$this->productRepo->create($validated);
       
       if($newProduct){
            return redirect()->route('Product')->with("success","product Save Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        }
    }
    
    public function edit($id){
        $getCategory =$this->productRepo->getCategory();
        $getVarient =$this->productRepo->getVarient();
        $getproduct =$this->productRepo->find($id);
        $getDetails =$this->productRepo->getDetails($id);
        $getSubCategory =$this->productRepo->getSubCategory();
        $getAllbrand = $this->productRepo->getAllbrand();
        return view('admin.product.productAdd',compact('getCategory','getVarient','getproduct','getSubCategory','getAllbrand','getDetails'));   
    }
    public function update(ProductRequest $request,$id){
       
       $validated = $request->validated();
        $category = Category::find($validated['category_id']);
        if ($category && $category->brand_id) {
            $validated['brand_id'] = $category->brand_id;
        }
       $newProduct =$this->productRepo->update($id,$validated);
       if($newProduct){
            return redirect()->route('Product')->with("success","Product update Successfully");
        }else{
            return redirect()->back()->with("errror","something went wrong");
        } 
    }
   public function delete($id){
        $getStaffSaveStatus= $this->productRepo->delete($id);
        if($getStaffSaveStatus){
            return redirect()->route('Product')->with("success","product delete Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }
    }
    
     public function deleteThisVarient($id)
    {
        $id = Crypto::decryptId($id);
        $variant = ProductDetail::find($id);
        if ($variant && $variant->delete()) {
            return redirect()->back('Product')->with("success", "Variant deleted successfully");
        }
        return redirect()->back()->with("error", "Something went wrong");
    }

    
    
    
    public function statusupdate($id,$status){
        
       $getStaffSaveStatus= $this->productRepo->statusupdate($id,$status);
        if($getStaffSaveStatus){
            return redirect()->route('Product')->with("success","product status update Successfully");
        }else{
            return redirect()->back()->with("error","something went wrong");
        }  
    }
    
    public function getSubcategory(Request $ajaxData){
      $getSubcategory= $this->productRepo->checkSubcategory($ajaxData->category_id);
      return response()->json(["data"=>$getSubcategory]);  
    }
    
    
    public function getMultiVarientSection(){
        $getVarient =$this->productRepo->getVarient();
        return view('admin.load.productVarient',compact('getVarient')); 
    }
    public function __destruct()
    {
       session()->flush();
    }
}
