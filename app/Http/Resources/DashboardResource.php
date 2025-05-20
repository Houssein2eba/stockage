<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'recentSales'=>$this['recentSales'],
            'dueAmount'=>$this['dueAmount'],
            'popular_products'=>$this['popular_products'],
            'paidAmount'=>$this['paidAmount'],
            'lowStockCount'=>$this['lowStockCount'],
            'totalProducts'=>$this['totalProducts'],
            'totalCategories'=>$this['totalCategories'],
            'totalSales'=>$this['totalSales'],
            'totalRevenue'=>$this['totalRevenue'],
            'totalProfit'=>$this['totalProfit'],
            'todaySales'=>$this['todaySales'],
            'todayRevenue'=>$this['todayRevenue'],
            ''

        ];
    }
}
