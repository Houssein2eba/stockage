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
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_roles',
        ];
        foreach($permissions as $permission){
            Permission::create(['name' => $permission]);
        }
        Admin::create([
            'name'=>'houssein',
            'email'=>'hanimation410@gmail.com',
            'email_verified_at'=>now(),
            'password'=>bcrypt('0000')
        ]);
        

        $role->syncPermissions($permissions);
        $user->syncPermissions($permissions);

    }
}
