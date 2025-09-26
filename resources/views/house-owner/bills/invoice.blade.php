<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $bill->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 28px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .invoice-info,
        .bill-info {
            width: 48%;
        }

        .invoice-info h3,
        .bill-info h3 {
            color: #28a745;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .bill-table th,
        .bill-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .bill-table th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }

        .bill-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total-section {
            text-align: right;
            margin-top: 20px;
        }

        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            border-top: 2px solid #28a745;
            padding-top: 10px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Multi-Tenant Flat & Bill Management System</p>
        <p>Generated on: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <div class="invoice-details">
        <div class="invoice-info">
            <h3>Bill Information</h3>
            <div class="info-row">
                <span class="info-label">Invoice #:</span> {{ $bill->id }}
            </div>
            <div class="info-row">
                <span class="info-label">Title:</span> {{ $bill->title }}
            </div>
            <div class="info-row">
                <span class="info-label">Category:</span> {{ $bill->category->name }}
            </div>
            <div class="info-row">
                <span class="info-label">Due Date:</span> {{ $bill->due_date->format('M d, Y') }}
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="status-badge status-{{ $bill->status }}">
                    {{ ucfirst($bill->status) }}
                </span>
            </div>
        </div>

        <div class="bill-info">
            <h3>Flat Information</h3>
            <div class="info-row">
                <span class="info-label">Flat Number:</span> {{ $bill->flat->flat_number }}
            </div>
            <div class="info-row">
                <span class="info-label">Floor:</span> {{ $bill->flat->floor }}
            </div>
            <div class="info-row">
                <span class="info-label">Building:</span> {{ $bill->flat->building->name }}
            </div>
            <div class="info-row">
                <span class="info-label">Address:</span> {{ $bill->flat->building->address }}
            </div>
            <div class="info-row">
                <span class="info-label">City:</span> {{ $bill->flat->building->city }}
            </div>
        </div>
    </div>

    @if ($bill->description)
        <div style="margin-bottom: 30px;">
            <h3 style="color: #28a745; margin-bottom: 10px;">Description</h3>
            <p style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; margin: 0;">
                {{ $bill->description }}
            </p>
        </div>
    @endif

    <table class="bill-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Category</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $bill->title }}</td>
                <td>{{ $bill->category->name }}</td>
                <td>৳{{ number_format($bill->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-amount">
            Total Amount: ৳{{ number_format($bill->amount, 2) }}
        </div>
    </div>

    @if ($bill->payments->count() > 0)
        <div style="margin-top: 40px;">
            <h3 style="color: #28a745; margin-bottom: 20px;">Payment History</h3>
            <table class="bill-table">
                <thead>
                    <tr>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Transaction ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bill->payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                            <td>৳{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                            <td>
                                <span class="status-badge status-{{ $payment->status }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated invoice. No signature required.</p>
        <p>For any queries, please contact the house owner.</p>
        <p>Generated by Multi-Tenant Flat & Bill Management System</p>
    </div>
</body>

</html>
