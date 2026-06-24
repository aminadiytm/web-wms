<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuList extends Model
{
    protected $primaryKey = 'menu_id';

    protected $fillable = [
        'menu_code',
        'menu_group',
        'menu_name',
        'menu_route',
        'menu_icon',
        'sort_order',
        'is_admin',
        'is_active',
    ];
}
