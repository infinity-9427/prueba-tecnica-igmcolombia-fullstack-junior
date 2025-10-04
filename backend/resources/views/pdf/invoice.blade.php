<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .company-details {
            color: #666;
            line-height: 1.5;
        }
        
        .invoice-title {
            text-align: right;
            flex: 1;
        }
        
        .invoice-title h1 {
            font-size: 28px;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .invoice-meta {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .invoice-details {
            flex: 1;
        }
        
        .client-details {
            flex: 1;
            margin-left: 30px;
        }
        
        .section-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .detail-row {
            margin-bottom: 5px;
        }
        
        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .items-table th {
            background-color: #007bff;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .items-table tr:hover {
            background-color: #e3f2fd;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .summary {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        
        .summary-table {
            width: 300px;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }
        
        .summary-table .label-col {
            text-align: right;
            font-weight: bold;
            background-color: #f8f9fa;
            width: 60%;
        }
        
        .summary-table .amount-col {
            text-align: right;
            width: 40%;
        }
        
        .total-row {
            background-color: #007bff !important;
            color: white !important;
            font-weight: bold;
            font-size: 14px;
        }
        
        .total-row td {
            border-bottom: none !important;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
        }
        
        .notes-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ config('app.name', 'Your Company') }}</div>
                <div class="company-details">
                    123 Business Street<br>
                    City, State 12345<br>
                    Phone: (555) 123-4567<br>
                    Email: info@company.com
                </div>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div style="font-size: 14px; color: #666;">
                    <span class="status-badge status-{{ strtolower($invoice->status) }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Invoice and Client Meta Information -->
        <div class="invoice-meta">
            <div class="invoice-details">
                <div class="section-title">Invoice Details</div>
                <div class="detail-row">
                    <span class="label">Invoice #:</span>
                    <strong>{{ $invoice->invoice_number }}</strong>
                </div>
                <div class="detail-row">
                    <span class="label">Issue Date:</span>
                    {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}
                </div>
                <div class="detail-row">
                    <span class="label">Due Date:</span>
                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                </div>
                <div class="detail-row">
                    <span class="label">Created:</span>
                    {{ $invoice->created_at->format('M d, Y H:i') }}
                </div>
            </div>
            
            <div class="client-details">
                <div class="section-title">Bill To</div>
                <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">
                    {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                </div>
                <div class="detail-row">
                    <span class="label">Document:</span>
                    {{ ucfirst($invoice->client->document_type) }}: {{ $invoice->client->document_number }}
                </div>
                @if($invoice->client->email)
                <div class="detail-row">
                    <span class="label">Email:</span>
                    {{ $invoice->client->email }}
                </div>
                @endif
                @if($invoice->client->phone)
                <div class="detail-row">
                    <span class="label">Phone:</span>
                    {{ $invoice->client->phone }}
                </div>
                @endif
            </div>
        </div>

        <!-- Invoice Description -->
        @if($invoice->description)
        <div style="margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
            <div class="section-title">Description</div>
            <p>{{ $invoice->description }}</p>
        </div>
        @endif

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 45%;">Description</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-right">Unit Price</th>
                    <th style="width: 10%;" class="text-right">Tax Rate</th>
                    <th style="width: 10%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                    $totalTax = 0;
                @endphp
                @foreach($invoice->items as $index => $item)
                    @php
                        $itemTotal = $item->quantity * $item->unit_price;
                        $itemTax = $itemTotal * ($item->tax_rate / 100);
                        $itemTotalWithTax = $itemTotal + $itemTax;
                        $subtotal += $itemTotal;
                        $totalTax += $itemTax;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if($item->description)
                                <br><small style="color: #666;">{{ $item->description }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->tax_rate, 1) }}%</td>
                        <td class="text-right">${{ number_format($itemTotalWithTax, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td class="label-col">Subtotal:</td>
                    <td class="amount-col">${{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label-col">Tax Total:</td>
                    <td class="amount-col">${{ number_format($totalTax, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label-col">TOTAL:</td>
                    <td class="amount-col">${{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Additional Notes -->
        @if($invoice->additional_notes)
        <div class="notes">
            <div class="notes-title">Additional Notes:</div>
            <p>{{ $invoice->additional_notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This invoice was generated on {{ now()->format('M d, Y \a\t H:i') }}</p>
            @if($invoice->status === 'pending')
                <p><strong>Payment is due by {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</strong></p>
            @endif
        </div>
    </div>
</body>
</html>