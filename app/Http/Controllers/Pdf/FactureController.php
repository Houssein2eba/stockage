<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureController extends Controller
{
    public function generatePdf($id)
    {
        $order = Order::with(['client', 'products', 'payment'])->findOrFail($id);
        
        $pdf = Pdf::loadView('pdf.facture', compact('order'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);
        return $pdf->download('invoice-' . $order->reference . '.pdf');
    }
}
