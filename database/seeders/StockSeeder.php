<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        // Create Categories
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories'
            ],
            [
                'name' => 'Clothing',
                'description' => 'Fashion and apparel'
            ],
            [
                'name' => 'Books',
                'description' => 'Books and publications'
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Home appliances and kitchenware'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Clients
        $clients = [
            [
                'name' => 'John Doe',
                'number' => '23456789'
            ],
            [
                'name' => 'Jane Smith',
                'number' => '24567890'
            ],
            [
                'name' => 'Robert Johnson',
                'number' => '25678901'
            ]
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }

        // Create Products
        $products = [
            [
                'name' => 'Laptop Pro',
                'description' => 'High-performance laptop',
                'price' => 999.99,
                'quantity' => 50,
                'min_quantity' => 10,
                'image' => 'products/laptop.jpg',
                'categories' => ['Electronics']
            ],
            [
                'name' => 'Cotton T-Shirt',
                'description' => 'Comfortable cotton t-shirt',
                'price' => 19.99,
                'quantity' => 100,
                'min_quantity' => 20,
                'image' => 'products/tshirt.jpg',
                'categories' => ['Clothing']
            ],
            [
                'name' => 'Coffee Maker',
                'description' => 'Automatic coffee maker',
                'price' => 79.99,
                'quantity' => 30,
                'min_quantity' => 5,
                'image' => 'products/coffee-maker.jpg',
                'categories' => ['Home & Kitchen', 'Electronics']
            ]
        ];

        // Create sample product images
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }

        foreach ($products as $productData) {
            $categories = $productData['categories'];
            unset($productData['categories']);
            
            $product = Product::create($productData);
            
            // Attach categories
            $categoryIds = Category::whereIn('name', $categories)->pluck('id');
            $product->categories()->attach($categoryIds);
        }
    }
}