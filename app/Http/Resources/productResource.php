<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class productResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'description'=>$this->description,
            'price'=>$this->price,
            'benefit' => $this->whenLoaded('orders', function () {
                $totalAmount = $this->orders->sum(function ($order) {
                    return $order->pivot->total_amount;
                });
                $totalCost = $this->orders->count() * $this->cost;
                return $totalAmount - $totalCost;
            }),
            'quantity' => $this->whenLoaded('stocks', function () {
                return $this->stocks->sum(function ($stock) {
                    return $stock->pivot->quantity;
                });
                }),
            'quantities_by_expiration' => $this->whenLoaded('stocks', function () {
                return $this->stocks->groupBy('pivot.expiration_date')->map(function ($group) {
                    return $group->sum('pivot.quantity');
                });
                 }),

            'image'=>$this->image,
            'pivot'=>$this->whenLoaded('pivot', fn () => $this->pivot),
            'categories'=>$this->whenLoaded('categories', fn () => $this->categories),
            'created_at'=>$this->created_at,
            'cost'=>$this->cost,
            'updated_at'=>$this->updated_at,
            'sold'=>$this->whenLoaded('orders', function () {
                return $this->orders->sum(function ($order) {
                    return $order->pivot->quantity;
                });
            }),


        ];
    }
}
