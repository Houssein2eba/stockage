<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ClearStockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
                        'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'location' => $this->location,
             'totalValue' => $this->products->sum(function ($product) {
                return $product->price * $product->pivot->products_quantity;
            }),

            'productsCount' => $this->products()->count() ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => productResource::collection($this->whenLoaded('products')),
        ];
    }
}
