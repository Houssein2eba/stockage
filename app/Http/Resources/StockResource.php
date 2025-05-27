<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'location' => $this->location,
            'totalValue' => $this->products()->sum(DB::raw('price * quantity')),
             'totalProducts'=>$this->products()->sum('quantity'),
            'productsCount' => $this->products()->where('quantity', '>', '0')->count() ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => productResource::collection($this->whenLoaded('products')),
            'paginated_products' => $request->wantsJson() ? null : $this->whenLoaded('products', function () {
    return [
        'data' => ProductResource::collection($this->products),
        'links' => [
            'first' => $this->products->url(1),
            'last' => $this->products->url($this->products->lastPage()),
            'prev' => $this->products->previousPageUrl(),
            'next' => $this->products->nextPageUrl(),
        ],
        'meta' => [
            'current_page' => $this->products->currentPage(),
            'last_page' => $this->products->lastPage(),
            'from' => $this->products->firstItem(),
            'to' => $this->products->lastItem(),
            'total' => $this->products->total(),
            'links' => $this->products->toArray()['links'],
        ],
    ];
}),
'paginated_lows' =>$request->wantsJson() ? null : $this->whenLoaded('lows', function () {
    return [
        'data' => ProductResource::collection($this->products),
        'links' => [
            'first' => $this->products->url(1),
            'last' => $this->products->url($this->products->lastPage()),
            'prev' => $this->products->previousPageUrl(),
            'next' => $this->products->nextPageUrl(),
        ],
        'meta' => [
            'current_page' => $this->products->currentPage(),
            'last_page' => $this->products->lastPage(),
            'from' => $this->products->firstItem(),
            'to' => $this->products->lastItem(),
            'total' => $this->products->total(),
            'links' => $this->products->toArray()['links'],
        ],
    ];
}),

        ];
    }
}
