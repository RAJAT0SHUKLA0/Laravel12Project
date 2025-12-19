<?php

namespace App\Repositories\Settings;

use App\Repositories\Settings\SubMenuRepositoryInterface;
use App\Models\SubMenu;
use App\Models\SubMenuType;
use App\Models\Menu;
use App\Utils\Uploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class SubMenuRepository implements SubMenuRepositoryInterface
{
    public function getAll()
    {
      return SubMenu::where('is_delete',0)->get();
    }
    public function find($id)
    {
       return SubMenu::where('id',$id)->first();  
    }

    public function create(array $data): bool
    {
        $sunmenu= SubMenu::orderby('order','desc')->latest()->first();
        if($sunmenu){
            $data['order'] = $sunmenu->order +1;
        }
        if (!empty($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $path = Uploads::uploadImage($data['image'], 'sub-menu');
            $data['image'] = $path;
        }
        if(!empty($data['parent_id'])){
            $data['parent_id'] =$data['parent_id'] ;
        }else{
            
        $data['parent_id'] = 0;
        }
        $data['action'] = Str::slug($data['name'],'_');
        return SubMenu::create($data) !== null;
    }
    
    public function update($id, array $data): bool
    {
        $menu = SubMenu::find($id);
        if (!$menu) return false;
    
        if (!empty($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old image
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = Uploads::uploadImage($data['image'], 'sub-menu');
        } else {
            unset($data['image']); // keep existing image if none uploaded
        }
        return $menu->update($data);
    }
    public function delete($id)
    {
      $stateDataSet = SubMenu::where('id',$id)->update(['is_delete'=>1]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }  
    }
    public function getAllMenu()
    {
      $stateDataSet = Menu::where('is_delete',0)->get(); 
      return $stateDataSet;
    }
    
     public function getAllType()
    {
      $stateDataSet = SubMenuType::where('is_delete',0)->get(); 
      return $stateDataSet;
    }
    
    public function getAllparent()
    {
      $stateDataSet = SubMenu::where('parent_id',0)->get(); 
      return $stateDataSet;
    }
}
