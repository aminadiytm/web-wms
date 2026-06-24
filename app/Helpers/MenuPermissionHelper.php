<?php

namespace App\Helpers;

use App\Models\MenuList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class MenuPermissionHelper
{
    public static function menuCode(string $indexRoute): ?string
    {
        return Cache::remember("menu_code_{$indexRoute}", 3600, function () use ($indexRoute) {
            return MenuList::where('menu_route', $indexRoute)
                ->where('is_active', true)
                ->value('menu_code');
        });
    }

    public static function can(string $indexRoute, string $action): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $menuCode = self::menuCode($indexRoute);

        if (!$menuCode) {
            return false;
        }

        $user = Auth::user();
    
        return $user
            ? Gate::forUser($user)->allows($menuCode . '_' . $action)
            : false;
        }

    public static function canView(string $indexRoute): bool
    {
        return self::can($indexRoute, 'view');
    }

    public static function canCreate(string $indexRoute): bool
    {
        return self::can($indexRoute, 'create');
    }

    public static function canEdit(string $indexRoute): bool
    {
        return self::can($indexRoute, 'edit');
    }

    public static function canDelete(string $indexRoute): bool
    {
        return self::can($indexRoute, 'delete');
    }
}