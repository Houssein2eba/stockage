<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'reference' => $this->reference,
            'status' => $this->status,
            'total_amount' => $this->products->sum('pivot.total_amount'),
            'client' => new ClientResource($this->whenLoaded('client')),
            'payment' => new PaymentResource($this->whenLoaded('payment')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'items' => $this->products->count(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
