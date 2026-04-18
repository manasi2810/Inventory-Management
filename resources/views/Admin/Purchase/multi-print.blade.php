<!DOCTYPE html>
<html>
<head>
    <title>Multiple Purchase Order Print</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 20px;
            color: #000;
        }

        /* EACH PO PAGE */
        .po-page {
            page-break-after: always;
            padding: 10px;
        }

        .po-page:last-child {
            page-break-after: auto;
        }

        /* HEADER */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        /* FLEX BOX */
        .flex {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            gap: 20px;
        }

        .box {
            width: 48%;
        }

        .box h4 {
            margin-bottom: 5px;
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
            background: #f2f2f2;
        }

        .right {
            text-align: right;
        }

        /* FOOTER */
        .footer {
            margin-top: 15px;
            font-size: 11px;
        }

        /* SIGNATURE */
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .sign {
            width: 180px;
            text-align: center;
        }

        /* PRINT BUTTON */
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

@foreach($purchases as $purchase)

<div class="po-page">

    {{-- HEADER --}}
    <div class="header">
        <h2>SMARTSTOCK PRIVATE LIMITED</h2>
        <p>Purchase Order System</p>
    </div>

    {{-- COMPANY + VENDOR --}}
    <div class="flex">

        {{-- COMPANY --}}
        <div class="box">
            <h4>Company Details</h4>
            <p><b>SmartStock Pvt. Ltd.</b></p>
            <p>Plot No. 12, Industrial Area</p>
            <p>Pune, Maharashtra - 411001</p>
            <p><b>Phone:</b> +91 7895644558</p>
            <p><b>Email:</b> manasinikam09@gmail.com</p>
            <p><b>GST:</b> 27ABCDE1234F1Z5</p>
        </div>

        {{-- VENDOR --}}
        <div class="box">
            <h4>Vendor Details</h4>
            <p><b>{{ $purchase->vendor->name }}</b></p>
            <p>{{ $purchase->vendor->address ?? '-' }}</p>
            <p><b>Phone:</b> {{ $purchase->vendor->phone ?? '-' }}</p>
            <p><b>Email:</b> {{ $purchase->vendor->email ?? '-' }}</p>
            <p><b>GST:</b> {{ $purchase->vendor->gst_no ?? '-' }}</p>
        </div>

    </div>

    {{-- PO INFO --}}
    <div style="margin-top: 15px;">
        <p><b>PO No:</b> {{ $purchase->invoice_no }}</p>
        <p><b>Date:</b> {{ $purchase->purchase_date }}</p>
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
        <p><b>Note:</b> Goods once delivered will not be returned.</p>
        <p><b>Disclaimer:</b> This is a system generated Purchase Order.</p>
    </div>

    {{-- SIGNATURE --}}
    <div class="signature">
        <div class="sign">Prepared By</div>
        <div class="sign">Approved By</div>
        <div class="sign">Vendor Signature</div>
    </div>

</div>

@endforeach

</body>
</html>