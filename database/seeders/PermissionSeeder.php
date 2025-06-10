<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions grouped by module
        $permissions= [
    // Clients
    'view_clients',
    'create_clients',
    'export_clients',
    'update_clients',
    'delete_clients',

    // Sales
    'view_sales',
    'create_sales',
    'mark_as_paid',
    'update_sales',
    'delete_sales',
    'generate_invoice',

    // Stocks
    'view_stocks',
    'create_stocks',
    'update_stocks',
    'delete_stocks',

    // Users
    'view_users',
    'create_users',
    'update_users',
    'delete_users',
    'export_users',

    // Activity Logs
    'view_activity_log',

    // Products
    'view_products',
    'create_products',
    'export_products',
    'update_products',
    'delete_products',

    // Categories
    'view_categories',
    'create_categories',
    'update_categories',
    'delete_categories',

    // Notifications
    'view_notifications',

];



        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create admin role and assign all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        // Create manager role with limited permissions
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view_products',
            'view_sales', 'create_sales',
            'view_clients', 'create_clients',
            
        ]);

        // Create cashier role with basic permissions
        $cashierRole = Role::create(['name' => 'cashier']);
        $cashierRole->givePermissionTo([
            'view_products',
            'view_sales', 'create_sales',
            'view_clients', 'create_clients',
            
        ]);
    }
}
