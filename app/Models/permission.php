<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class permission extends Model
{
    protected $table = 'tbl_permission';

    protected $appends = ['menus', 'submenus'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    protected function menus(): Attribute
    {
        return new Attribute(
            get: fn () => Menu::whereIn('id', explode(',', $this->menu_id))->get()
        );
    }

    protected function submenus(): Attribute
    {
        return new Attribute(
            get: fn () => SubMenu::whereIn('id', explode(',', $this->sub_menu))->get()
        );
    }
}
