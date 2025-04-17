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
        $manager = Role::create([
            'name' => 'manager',
        ]);

        $admin=Role::create([
            'name' => 'admin',
        ]);
        $user=User::create([
            'name' => 'manager',
            'email' => 'ebahoussein@gmail.com',
            'email_verified_at' => now(),
            'number'=>'12345678',
            'password' => bcrypt('0000'), // password
        ])->assignRole($manager);
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
        $user=User::create([
            'name'=>'houssein',
            'email'=>'hanimation410@gmail.com',
            'number'=>'22345678',
            'email_verified_at'=>now(),
            'password'=>bcrypt('0000')
        ])->assignRole($admin);

         $admin->syncPermissions($permissions);
        $manager->syncPermissions([
            'create product',
            'edit product',
            'delete product',
            'show product',
            'create category',
            'edit category',
        ]);


        //$this->call(PermissionSeeder::class);
    }
}
