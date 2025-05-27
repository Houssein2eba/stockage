<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Log;

class FactureController extends Controller
{
//     public function generatePdf(Request $request,$id)
//     {
//         $order = Order::with(['client', 'products'])->findOrFail($id);

//         $pdf = Pdf::loadView('pdf.facture', compact('order'));
//         $pdf->setPaper('A4', 'portrait');
//         $pdf->setOptions([
//             'isHtml5ParserEnabled' => true,
//             'isRemoteEnabled' => true,
//             'isFontSubsettingEnabled' => true,
//             'isPhpEnabled' => true,
//             'defaultFont' => 'sans-serif',
//         ]);
//         if($request->wantsJson()){
//             return response()->json([
//                 'pdf' => base64_encode($pdf->output()),
//                 'order' => new OrderResource($order)
//             ]);
//         }
//         return $pdf->download('invoice-' . $order->reference . '.pdf');
//     }

public function generatePdf(Request $request, $id)
{
    $order = Order::with(['client', 'products'])->findOrFail($id);

    // Generate PDF with proper error handling
    try {
        $pdf = Pdf::loadView('pdf.facture', compact('order'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'pdf' => base64_encode($pdf->output()),
                'order' => new OrderResource($order),
                'filename' => 'invoice-' . $order->reference . '.pdf'
            ]);
        }

        return $pdf->download('invoice-' . $order->reference . '.pdf');

    } catch (\Exception $e) {
        Log::error("PDF Generation Error: " . $e->getMessage());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF',
                'error' => $e->getMessage()
            ], 500);
        }

        abort(500, 'Failed to generate PDF');
    }
}

}
