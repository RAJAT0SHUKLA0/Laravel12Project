<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class SubMenu extends Model
{
    protected $fillable =['name','menu_id','type','parent_id','image','color_code','action','order'];
    protected $table="tbl_sub_menu";
    
    
    public function menu() {
    return $this->belongsTo(Menu::class);
}

    public function submenutype() {
        return $this->belongsTo(SubMenuType::class,'type');
    }
}
