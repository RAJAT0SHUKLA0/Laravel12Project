<?php

namespace App\Http\Controllers\admin\setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Settings\MenuRepositoryInterface;
use App\Http\Requests\settings\MenuRequest;

class MenuController extends Controller
{
    
    private $menuRepo;
    public function __construct(MenuRepositoryInterface $MenuRepositoryInterface)
    {
        $this->menuRepo = $MenuRepositoryInterface;
    }
        public function index(MenuRequest $request, $id = null)
        {
            if ($request->isMethod('post')) {
                $validated = $request->validated();
                $menuCreate = $this->menuRepo->create($validated);
                return $menuCreate
                    ? redirect()->route('Menu')->with('success', 'Menu saved successfully')
                    : redirect()->back()->with('error', 'Something went wrong');
            }
            if ($request->isMethod('put')) {
                $validated = $request->validated();
                $menuUpdate = $this->menuRepo->update($id, $validated);
                return $menuUpdate
                    ? redirect()->route('Menu')->with('success', 'Menu updated successfully')
                    : redirect()->back()->with('error', 'Something went wrong');
            }
            $getMenu = $this->menuRepo->getAll();
            return view('admin.home.menu', compact('getMenu'));
        }
    
    public function edit($id){
       $getMenu = $this->menuRepo->getAll();
       $menu =$this->menuRepo->find($id);
       return view('admin.home.menu',compact('menu','getMenu')); 
    }
    
    public function delete($id)
    {
         $delete =$this->menuRepo->delete($id);
        if($delete){
        return redirect()->back()->with("success","menu Delete Successfully");
        }
              

        
    }
}
