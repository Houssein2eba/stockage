<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin=User::create([
            'name' => 'Admin',
            'email' => 'hanimation410@gmail.com',
            'number' => '23456789',
            'password' => bcrypt('0000'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $manager=User::create([
            'name' => 'Manager',
            'email' => 'manager@manager.com',
            'number' => '23456729',
            'password' => bcrypt('0000'),
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('manager');
        $cashier=User::create([
            'name' => 'Cashier',
            'email' => 'cashier@cashier.com',
            'number' => '23456739',
            'password' => bcrypt('0000'),
            'email_verified_at' => now(),
        ]);
        $cashier->assignRole('cashier');
    }
}
