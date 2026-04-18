<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 25px;
            color: #000;
        }

        /* HEADER */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        /* TWO COLUMN SECTION */
        .flex {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 10px;
        }

        .box {
            width: 48%;
        }

        .box h4 {
            margin: 0 0 6px;
            font-size: 13px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        .box p {
            margin: 2px 0;
            font-size: 12px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 6px;
            font-size: 12px;
        }

        th {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        /* FOOTER */
        .footer {
            margin-top: 20px;
            font-size: 11px;
        }

        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .sign {
            text-align: center;
            width: 200px;
        }

        .print-btn {
            margin-bottom: 10px;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

<button class="print-btn" onclick="window.print()">Print</button>

{{-- COMPANY HEADER --}}
<div class="header">
    <h2>SMARTSTOCK PRIVATE LIMITED</h2>
    <p>Purchase Order System</p>
</div>

{{-- TWO COLUMN INFO --}}
<div class="flex">

    {{-- COMPANY DETAILS --}}
    <div class="box">
        <h4>Company Details (Bill From)</h4>
        <p><b>SmartStock Pvt. Ltd.</b></p>
        <p>Plot No. 12, Industrial Area</p>
        <p>Pune, Maharashtra - 411001</p>
        <p><b>Phone:</b> +91 7895644558</p>
        <p><b>Email:</b> manasinikam09@gmail.com</p>
        <p><b>GST No:</b> 27ABCDE1234F1Z5</p>
    </div>

    {{-- VENDOR DETAILS --}}
    <div class="box">
        <h4>Vendor Details (Bill To)</h4>
        <p><b>{{ $purchase->vendor->name }}</b></p>
        <p>{{ $purchase->vendor->address ?? '-' }}</p>
        <p><b>Phone:</b> {{ $purchase->vendor->phone ?? '-' }}</p>
        <p><b>Email:</b> {{ $purchase->vendor->email ?? '-' }}</p>
        <p><b>GST No:</b> {{ $purchase->vendor->gst_no ?? '-' }}</p>
    </div>

</div>

{{-- PO INFO --}}
<div style="margin-top: 15px;">
    <p><b>PO No:</b> {{ $purchase->invoice_no }}</p>
    <p><b>PO Date:</b> {{ $purchase->purchase_date }}</p>
</div>

{{-- ITEMS TABLE --}}
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($purchase->items as $key => $item)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->qty }}</td>
            <td class="right">₹ {{ number_format($item->price, 2) }}</td>
            <td class="right">₹ {{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4" class="right">GRAND TOTAL</th>
            <th class="right">₹ {{ number_format($purchase->total_amount, 2) }}</th>
        </tr>
    </tfoot>
</table>

{{-- FOOTER --}}
<div class="footer">
    <p><b>Note:</b> Goods once delivered will not be taken back.</p>
    <p><b>Disclaimer:</b> This is a system generated Purchase Order.</p>
</div>

{{-- SIGNATURE --}}
<div class="signature">

    <div class="sign">
        ___________________<br>
        Prepared By
    </div>

    <div class="sign">
        ___________________<br>
        Approved By
    </div>

    <div class="sign">
        ___________________<br>
        Vendor Signature
    </div>

</div>

</body>
</html>