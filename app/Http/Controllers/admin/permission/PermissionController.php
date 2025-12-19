<?php

namespace App\Http\Controllers\admin\permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\permission;
use App\Models\Menu;
use App\Models\SubMenu;
use App\Models\Role;

class PermissionController extends Controller
{
        public function index(Request $request, $id=null)
        {
            $subchild= [];
            
            if ($request->isMethod('post')) {
                $Permmission = new permission;
                $Permmission->menu_id =is_array($request->menu_id)?implode(',',$request->menu_id):[]; 
                $Permmission->sub_menu =is_array($request->sub_menu_id)?implode(',',$request->sub_menu_id):[];
                // $Permmission->sub_child_menu =is_array($request->sub_child_menu)?implode(',',$request->sub_child_menu):[];
                $Permmission->role_id =$request->role_id;
                if( $Permmission->save()){
                  redirect()->route('Permission')->with('success', 'Permission saved successfully');  
                }else{
                   redirect()->back()->with('error', 'Something went wrong'); 
                }

              
            }
            if ($request->isMethod('put')) {
                $Permmission = permission::where('id', $id)->first(); 
            
                if ($Permmission) {
                    $Permmission->menu_id = is_array($request->menu_id) ? implode(',', $request->menu_id) : '';
                    $Permmission->sub_menu = is_array($request->sub_menu_id) ? implode(',', $request->sub_menu_id) : '';
                    $Permmission->role_id = $request->role_id;
            
                    if ($Permmission->save()) {
                        return redirect()->route('Permission')->with('success', 'Permission updated successfully');
                    } else {
                        return redirect()->back()->with('error', 'Something went wrong');
                    }
                } else {
                    return redirect()->back()->with('error', 'Permission not found');
                }
            }

            $getrole = Role::get();
            $getPermmission = Permission::get();
       
            $getmenu = Menu::where('is_delete',0)->get();
            $getsub_menu = SubMenu::where('is_delete',0)->get();
            foreach($getsub_menu as $child){
                 $getsub_menus = SubMenu::where('parent_id',$child->id)->where('is_delete',0)->get();
                 $subchild[]=$getsub_menus;
            }

            return view('admin.permission.permission', compact('getPermmission','subchild','getsub_menu','getmenu','getrole'));
        }
        public function edit($id){
             $subchild= [];
            $getrole = Role::get();
                $getPermmission = Permission::get();
                 $getPermmissionInfo = Permission::find($id);
                    $getmenu = Menu::where('is_delete',0)->get();
                    $getsub_menu = SubMenu::where('is_delete',0)->get();
                foreach($getsub_menu as $child){
                     $getsub_menus = SubMenu::where('parent_id',$child->id)->where('is_delete',0)->get();
                     $subchild[]=$getsub_menus;
                }
             return view('admin.permission.permission', compact('getPermmission','subchild','getsub_menu','getmenu','getrole','getPermmissionInfo'));
        }
}
