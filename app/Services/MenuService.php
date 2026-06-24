<?php
namespace App\Services;

use App\Models\MenuList;
use Illuminate\Support\Facades\Auth;

class MenuService
{
    public function getSidebarMenus()
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        return MenuList::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($menu) use ($user) {
                return $user->can($menu->menu_code . '_view');
            })
            ->groupBy(function ($menu) {
                return $menu->menu_group ?: 'Main';
            });
    }

}