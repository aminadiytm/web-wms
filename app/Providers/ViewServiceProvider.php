<?php

namespace App\Providers;

use App\Services\MenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.navbars.auth.sidebar', function ($view) {
            $menus = Auth::check()
                ? app(MenuService::class)->getSidebarMenus()
                : collect();

            $view->with('sidebarMenus', $menus);
        });
    }
}