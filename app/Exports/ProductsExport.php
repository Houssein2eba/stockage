<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::all();
    }

    public function headings():array
    {
        return [
            'name',
            'description',
            'price',
            'quantity',
            'min_quantity',
            'created_at',
            'updated_at'
        ];
    }
    public function map($product):array
    {
        return [
            $product->name,
            $product->description,
            $product->price,
            $product->quantity,
            $product->min_quantity,
            $product->created_at,
            $product->updated_at
        ];
    }


 
    
}
