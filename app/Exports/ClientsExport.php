<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\OrderDetail;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromArray, WithHeadings, WithMapping
{
    /**
     * @return array
     */
    public function array(): array
    {
        return Client::with(['orders' => function($query) {
                $query->where('status', 'pending')
                      ;
            }])
            ->select('clients.id', 'clients.name', 'clients.number')
            ->addSelect([
                'pending_amount' => OrderDetail::selectRaw('SUM(order_details.total_amount)')
                    ->join('orders', 'order_details.order_id', '=', 'orders.id')
                    ->whereColumn('orders.client_id', 'clients.id')
                    ->where('orders.status', 'pending')
            ])
  
            ->get()
            ->toArray();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            
            'Client Name',
            'Phone Number',
            'Total Pending Amount',
            'Pending Orders Count',
            
        ];
    }

    /**
     * @param mixed $client
     * @return array
     */
    public function map($client): array
    {
        return [
           
            $client['name'] ?? '',
            $client['number'] ?? '',
            $client['pending_amount'] ?? 0,
            count($client['orders']) ?? 0,
            
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'D' => '#,##0.00', // Format pending amount as currency
        ];
    }
}