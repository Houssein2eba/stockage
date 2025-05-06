<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;

namespace App\Exports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ClientExport implements FromCollection, WithHeadings, WithEvents
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function collection(): Collection
    {
        $orders = $this->client->orders()->with('products')->get();

        $rows = collect();
        $totalPaid = 0;
        $totalDebts = 0;

        foreach ($orders as $order) {
            $productNames = $order->products->pluck('name')->implode(', ');
            $amount = $order->products->sum(fn ($p) => $p->pivot->quantity * $p->price);

            // Calculate paid and unpaid amounts
            if ($order->status === 'paid') {
                $totalPaid += $amount;
            } else {
                $totalDebts += $amount;
            }

            $rows->push([
                'reference' => $order->reference,
                'products' => $productNames,
                'date' => $order->created_at->format('d/m/Y'),
                'amount' => $amount,
                'status' => $order->status,
                'paid_amount' => $order->status === 'paid' ? $amount : 0,
                'unpaid_amount' => $order->status !== 'paid' ? $amount : 0,
            ]);
        }

        // Add total row
        $total = $rows->sum('amount');
        $rows->push([
            'reference' => '',
            'products' => 'Total',
            'date' => '',
            'amount' => $total,
            'status' => '',
            'paid_amount' => $totalPaid,
            'unpaid_amount' => $totalDebts,
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Reference',
            'Products',
            'Date',
            'Amount',
            'Status',
            'Paid Amount',
            'Unpaid Amount'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->insertNewRowBefore(1, 2);
                $event->sheet->setCellValue('A1', 'Client: ' . $this->client->name);
                $event->sheet->mergeCells('A1:C1');
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            }
        ];
    }
}

