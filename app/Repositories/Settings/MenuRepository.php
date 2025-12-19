<?php

namespace App\Repositories\Settings;

use App\Repositories\Settings\MenuRepositoryInterface;
use App\Models\Menu;
class MenuRepository implements MenuRepositoryInterface
{
    public function getAll()
    {
      return Menu::where('is_delete',0)->get();
    }
    public function find($id)
    {
       return Menu::where('id',$id)->first();  
    }

    public function create(array $data)
    {
       $menulastest =Menu::orderby('orderby','desc')->latest()->first();
       if($menulastest){
          $data['orderby'] = $menulastest->orderby +1;
       }
       $stateDataSet = Menu::create($data);
       
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
    }
    public function update($id, array $data)
    {
       $stateDataSet = Menu::where('id',$id)->update($data); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }
        
    }
    public function delete($id)
    {
      $stateDataSet = Menu::where('id',$id)->update(['is_delete'=>1]); 
       if($stateDataSet){
         return true;
       }else{
         return false;
       }  
    }
}
