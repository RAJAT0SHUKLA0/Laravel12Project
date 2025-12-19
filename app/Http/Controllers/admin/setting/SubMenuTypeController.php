<?php

namespace App\Http\Controllers\admin\setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Settings\SubMenuTypeRepositoryInterface;
use App\Http\Requests\settings\SubMenuTypeRequest;

class SubMenuTypeController extends Controller
{
   private $submenutypeRepo;
    public function __construct(SubMenuTypeRepositoryInterface $SubMenuTypeRepositoryInterface)
    {
        $this->submenutypeRepo = $SubMenuTypeRepositoryInterface;
    }
        public function index(SubMenuTypeRequest $request, $id = null)
        {
            if ($request->isMethod('post')) {
                $validated = $request->validated();
                $menuCreate = $this->submenutypeRepo->create($validated);
                return $menuCreate
                    ? redirect()->route('SubMenuType')->with('success', 'Menu saved successfully')
                    : redirect()->back()->with('error', 'Something went wrong');
            }
            if ($request->isMethod('put')) {
                $validated = $request->validated();
                $menuUpdate = $this->submenutypeRepo->update($id, $validated);
                return $menuUpdate
                    ? redirect()->route('SubMenuType')->with('success', 'Menu updated successfully')
                    : redirect()->back()->with('error', 'Something went wrong');
            }
            $getMenu = $this->submenutypeRepo->getAll();
            return view('admin.home.SubMenuType', compact('getMenu'));
        }
    
    public function edit($id){
       $getMenu = $this->submenutypeRepo->getAll();
       $menu =$this->submenutypeRepo->find($id);
       return view('admin.home.SubMenuType',compact('menu','getMenu')); 
    }
    
    public function delete($id)
    {
        $delete =$this->submenutypeRepo->delete($id);
        if($delete){
         return redirect()->back()->with("success","menu Delete Successfully");
        }
    }
}
