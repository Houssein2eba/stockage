<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $role = Role::create([
            'name' => 'manager',
            'guard_name' => 'web',
        ]);
        $user=User::create([
            'name' => 'manager',
            'email' => 'ebahoussein@gmail.com',
            'email_verified_at' => now(),
            'number'=>'12345678',
            'password' => bcrypt('0000'), // password
        ])->assignRole($role);
        $permissions=[
            'create product',
            'edit product',
            'delete product',
            'show product',
            'create category',
            'edit category',
            'delete category',
            'show category',
            'create user',
            'edit user',
            'delete user',
            'show user',
            'create role',
            'edit role',
            'delete role',
            'show role',
            'create permission',
            'edit permission',
            'delete permission',
            'show permission',
            'create admin',
            'edit admin',
            'delete admin',
            'show admin',
        ];
        foreach($permissions as $permission){
            Permission::create(['name' => $permission]);
        }
        $admin=Admin::create([
            'name'=>'houssein',
            'email'=>'hanimation410@gmail.com',
            'email_verified_at'=>now(),
            'password'=>bcrypt('0000')
        ]);

         $admin->syncPermissions($permissions);
        $role->syncPermissions([
            'create product',
            'edit product',
            'delete product',
            'show product',
            'create category',
            'edit category',
        ]);
        $user->syncPermissions($permissions,);

        //$this->call(PermissionSeeder::class);
    }
}
