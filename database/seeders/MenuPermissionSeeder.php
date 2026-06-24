<?php

namespace Database\Seeders;

use App\Models\MenuList;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class MenuPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $menus = [
            [
                'menu_code' => 'MNU001',
                'menu_group' => null,
                'menu_name' => 'Dashboard',
                'menu_route' => 'dashboard',
                'menu_icon' => 'fas fa-chart-line',
                'sort_order' => 1,
            ],

            [
                'menu_code' => 'MNU101',
                'menu_group' => 'Master Data',
                'menu_name' => 'Category',
                'menu_route' => 'masterdata.catIndex',
                'menu_icon' => 'fas fa-tags',
                'sort_order' => 101,
            ],
            [
                'menu_code' => 'MNU102',
                'menu_group' => 'Master Data',
                'menu_name' => 'Product',
                'menu_route' => 'masterdata.prdIndex',
                'menu_icon' => 'fas fa-cube',
                'sort_order' => 102,
            ],
            [
                'menu_code' => 'MNU103',
                'menu_group' => 'Master Data',
                'menu_name' => 'Warehouse',
                'menu_route' => 'masterdata.whIndex',
                'menu_icon' => 'fas fa-warehouse',
                'sort_order' => 103,
            ],
            [
                'menu_code' => 'MNU104',
                'menu_group' => 'Master Data',
                'menu_name' => 'Location',
                'menu_route' => 'masterdata.locIndex',
                'menu_icon' => 'fas fa-map-marker-alt',
                'sort_order' => 104,
            ],
            [
                'menu_code' => 'MNU105',
                'menu_group' => 'Master Data',
                'menu_name' => 'Routing Approval',
                'menu_route' => 'masterdata.approvalRouteIndex',
                'menu_icon' => 'fas fa-user-check',
                'sort_order' => 105,
            ],

            [
                'menu_code' => 'MNU201',
                'menu_group' => 'Transactions',
                'menu_name' => 'Inbound',
                'menu_route' => 'transaction.inbIndex',
                'menu_icon' => 'fas fa-dolly-flatbed',
                'sort_order' => 201,
            ],
            [
                'menu_code' => 'MNU202',
                'menu_group' => 'Transactions',
                'menu_name' => 'Outbound',
                'menu_route' => 'transaction.outbIndex',
                'menu_icon' => 'fas fa-shipping-fast',
                'sort_order' => 202,
            ],

            [
                'menu_code' => 'MNU301',
                'menu_group' => 'Inventory',
                'menu_name' => 'Stock Inventory',
                'menu_route' => 'inventory.stockIndex',
                'menu_icon' => 'fas fa-boxes',
                'sort_order' => 301,
            ],

            [
                'menu_code' => 'MNU401',
                'menu_group' => 'Security',
                'menu_name' => 'Role Permission',
                'menu_route' => 'security.roleIndex',
                'menu_icon' => 'fas fa-user-shield',
                'sort_order' => 401,
                'is_admin' => true,
            ],
            [
                'menu_code' => 'MNU402',
                'menu_group' => 'Security',
                'menu_name' => 'Admin Users',
                'menu_route' => 'admin.users.usrIndex',
                'menu_icon' => 'fas fa-users',
                'sort_order' => 402,
                'is_admin' => true,
            ],
        ];

        foreach ($menus as $item) {
            $menu = MenuList::updateOrCreate(
                ['menu_code' => $item['menu_code']],
                [
                    'menu_group' => $item['menu_group'],
                    'menu_name' => $item['menu_name'],
                    'menu_route' => $item['menu_route'],
                    'menu_icon' => $item['menu_icon'],
                    'sort_order' => $item['sort_order'],
                    'is_admin' => $item['is_admin'] ?? false,
                    'is_active' => true,
                ]
            );

            foreach (['_view', '_create', '_edit', '_delete'] as $suffix) {
                Permission::firstOrCreate([
                    'name' => $menu->menu_code . $suffix,
                    'guard_name' => 'web',
                ]);
            }
        }

        $admin = Role::firstOrCreate([
            'name' => 'ADMIN',
            'guard_name' => 'web',
        ]);

        $staffInbound = Role::firstOrCreate([
            'name' => 'STAFF_INBOUND',
            'guard_name' => 'web',
        ]);

        $staffOutbound = Role::firstOrCreate([
            'name' => 'STAFF_OUTBOUND',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions(Permission::pluck('name')->toArray());

        $staffInbound->syncPermissions([
            'MNU001_view',

            'MNU101_view',
            'MNU102_view',

            'MNU201_view',
            'MNU201_create',
            'MNU201_edit',
            'MNU201_delete',

            'MNU202_view',

            'MNU301_view',
        ]);

        $staffOutbound->syncPermissions([
            'MNU001_view',

            'MNU101_view',
            'MNU102_view',

            'MNU201_view',

            'MNU202_view',
            'MNU202_create',
            'MNU202_edit',
            'MNU202_delete',

            'MNU301_view',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}