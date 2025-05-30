<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Categories
        $categoryData = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Books', 'description' => 'Books and publications'],
            ['name' => 'Home & Kitchen', 'description' => 'Home appliances and kitchenware'],
        ];

        $categories = collect();
        foreach ($categoryData as $data) {
            $categories->push(Category::create($data));
        }

        // 2. Create Clients
        $clients = [
            ['name' => 'John Doe', 'number' => '23456789'],
            ['name' => 'Jane Smith', 'number' => '24567890'],
            ['name' => 'Robert Johnson', 'number' => '25678901'],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }

        // 3. Create Stocks
        $stocks = Stock::factory()->count(10)->create();

        // 4. Create Products
        $products = Product::factory()->count(50)->create();

        foreach ($products as $product) {
            // Attach Stocks
            $assignedStocks = $stocks->random(rand(1, 3));
            foreach ($assignedStocks as $stock) {
                $product->stocks()->attach($stock->id, [
                    'stock_in_date' => Carbon::now()->subDays(rand(1, 30)), // Random stock in date within the last 30 days
                    'stock_out_date' => null, // Initially no stock out date
                ]);

            }

            // Attach Categories (1 to 2 random categories)
            $categoryIds = $categories->random(rand(1, 2))->pluck('id');
            $product->categories()->attach($categoryIds);
        }

        // 5. Create product image folder
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }
    }
}
?>
