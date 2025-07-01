<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::with('client', 'products')->get()->map(function ($order) {
            return [
                'Reference' => $order->reference,
                'Client Name' => $order->client ? $order->client->name : 'N/A',
                'Total Amount' => $order->order_total_amount,
                'Status' => $order->status,
                'Created At' => $order->created_at,
                'Products' => $order->products->map(function ($product) {
                    return $product->name . ' (' . $product->pivot->quantity . ')';
                })->implode(', '),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Reference',
            'Client Name',
            'Total Amount',
            'Status',
            'Created At',
            'Products',
        ];
    }
}
