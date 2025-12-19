<?php

namespace App\Http\Controllers\admin\setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Settings\SubMenuRepositoryInterface;
use App\Http\Requests\settings\SubMenuRequest;

class SubMenuController extends Controller
{
   private $submenuRepo;
    public function __construct(SubMenuRepositoryInterface $SubMenuRepositoryInterface)
    {
        $this->submenuRepo = $SubMenuRepositoryInterface;
    }
        public function index(SubMenuRequest $request, $id = null)
        {
            if ($request->isMethod('post')) {
                $validated = $request->validated();
                $menuCreate =$this->submenuRepo->create($validated);
                return $menuCreate
                    ? redirect()->route('SubMenu')->with('success', 'Menu saved successfully')
                    : redirect()->back()->with('error', 'Something went wrong');
            }
            if ($request->isMethod('put')) {
                $validated = $request->validated();
                $menuUpdate = $this->submenuRepo->update($id, $validated);
                return $menuUpdate
                    ? redirect()->route('SubMenu')->with('success', 'Menu updated successfully')
                    : redirect()->back()->with('error', 'Something went wrong');
            }
            $getMenu = $this->submenuRepo->getAll();
            $getAllMenu = $this->submenuRepo->getAllMenu();
            $getAllType = $this->submenuRepo->getAllType();
            $getAllparent = $this->submenuRepo->getAllparent();
            return view('admin.home.subMenu', compact('getMenu','getAllMenu','getAllType','getAllparent'));
        }
    
    public function edit($id){
       $getMenu = $this->submenuRepo->getAll();
       $menu = $this->submenuRepo->find($id);
         $getAllMenu = $this->submenuRepo->getAllMenu();
            $getAllType = $this->submenuRepo->getAllType();
            $getAllparent = $this->submenuRepo->getAllparent();
       return view('admin.home.subMenu',compact('menu','getMenu','getAllMenu','getAllType','getAllparent')); 
    }
    
    public function delete($id)
    {
         $delete =$this->menuRepo->delete($id);
        if($delete){
        return redirect()->back()->with("success","menu Delete Successfully");
        }
              

        
    }
}
