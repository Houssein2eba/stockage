<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [
            "id"=> $this->id,
            "name"=>$this->name,
            "number"=>$this->number,
            "created_at"=>$this->created_at->format('Y-m-d H:i:s'),
            'orders'=> $this->whenLoaded('orders', function () {
                return OrderResource::collection($this->orders);
            }),
            'orders_count'=> $this->whenLoaded('orders', function () {
                return $this->orders->count();
            }),
            'depts_amount'=> $this->whenLoaded('orders', function () {
                return $this->orders->where('status', 'pending')->sum('total_amount');
            }),
        ];
    }
}
