<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class, // This will handle all permissions and roles
            AdminSeeder::class,
            StockSeeder::class,
        ]);
    }
}
