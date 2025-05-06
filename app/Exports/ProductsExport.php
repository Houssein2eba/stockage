<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection,WithHeadings,WithMapping
{
    protected $products;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $products = Product::with('categories')->get();

        $rows = collect();

        foreach ($products as $product) {
            $categories = $product->categories->pluck('name')->implode(', ');
            $rows->push([
                'name' => $product->name,
                'description' => $product->description,
                'category' => $categories,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'min_quantity' => $product->min_quantity,
                'created_at' => $product->created_at->format('d/m/Y'),

            ]);
        }
        return $rows;
    }

    public function headings():array
    {
        return [
            'name',
            'description',
            'category',
            'price',
            'quantity',
            'min_quantity',
            'created_at',

        ];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['description'],
            $row['category'],
            $row['price'],
            $row['quantity'],
            $row['min_quantity'],
            $row['created_at'],
        ];
    }
}
