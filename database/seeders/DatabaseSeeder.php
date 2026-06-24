<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call([
            MenuPermissionSeeder::class,
            UserSeeder::class,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}