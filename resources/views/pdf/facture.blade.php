<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->reference }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #1a1a1a;
        }
        .container {
            padding: 40px;
        }
        .header {
            margin-bottom: 40px;
        }
        .company-info {
            float: left;
        }
        .invoice-info {
            float: right;
            text-align: right;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .clear {
            clear: both;
        }
        .client-info {
            margin: 40px 0;
            padding: 20px 0;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        .client-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        th {
            background-color: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .payment-info {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-paid {
            background-color: #def7ec;
            color: #03543f;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-cancelled {
            background-color: #fef2f2;
            color: #991b1b;
        }
        .status-container {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                @php
                    $settings = \App\Models\Setting::get()->first();
                @endphp
                <div class="company-name">{{ $settings->company_name }}</div>
                <div>Address: {{ $settings->address }}</div>
                <div>Téléphone: {{ $settings->phone }}</div>
                <div>Email: {{ $settings->email }}</div>
            </div>
            <div class="invoice-info">
                <div class="invoice-number">#{{ $order->reference }}</div>
                <div>Date: {{ $order->created_at->format('d/m/Y') }}</div>
                <div class="status-container">
                    <span>Status:
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Client Info -->
        <div class="client-info">
            <div class="client-title">Client Information</div>
            @if($order->client)
                <div>{{ $order->client->name }}</div>
                <div>{{ $order->client->number }}</div>
            @else
                <div>Client Not Specified</div>
            @endif
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td class="text-right">{{ number_format($product->price, 2, ',', ' ') }} MRU</td>
                        <td class="text-right">{{ $product->pivot->quantity }}</td>
                        <td class="text-right">{{ number_format($product->pivot->total_amount, 2, ',', ' ') }} MRU</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total</td>
                    <td class="text-right">{{ number_format($order->total_amount, 2, ',', ' ') }} MRU</td>
                </tr>
            </tbody>
        </table>



        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
