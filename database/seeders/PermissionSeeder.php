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
        $permissions = [
            // Product Permissions
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'export_products',

            // Category Permissions
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',

            // Order/Sales Permissions
            'view_sales',
            'create_sales',
            'edit_sales',
            'delete_sales',
            'export_sales',
            'change_order_status',

            // Client Permissions
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
            'export_clients',

            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'export_users',

            // Role & Permission Management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',

            // Activity Log
            'view_activity_log',

            // Payment Methods
            'view_payment_methods',
            'create_payment_methods',
            'edit_payment_methods',
            'delete_payment_methods',

            // Dashboard & Reports
            'view_dashboard',
            'view_reports',
            'export_reports',

            // Notifications
            'view_notifications',
            'manage_notifications',

            // Stock Management
            'manage_stock',
            'view_low_stock',
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
            'view_products', 'create_products', 'edit_products',
            'view_categories',
            'view_sales', 'create_sales', 'edit_sales', 'export_sales',
            'view_clients', 'create_clients', 'edit_clients',
            'view_users',
            'view_dashboard',
            'view_reports',
            'view_notifications',
            'manage_stock',
            'view_low_stock',
        ]);

        // Create cashier role with basic permissions
        $cashierRole = Role::create(['name' => 'cashier']);
        $cashierRole->givePermissionTo([
            'view_products',
            'view_sales', 'create_sales',
            'view_clients', 'create_clients',
            'view_notifications',
        ]);
    }
}
