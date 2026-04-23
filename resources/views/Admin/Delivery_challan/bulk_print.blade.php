<!DOCTYPE html>
<html>
<head>
    <title>Multiple Delivery Challan Print</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 20px;
            color: #000;
        }

        
        .dc-page {
            page-break-after: always;
            padding: 10px;
        }

        .dc-page:last-child {
            page-break-after: auto;
        }

         
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
            margin: 0 0 6px;
            font-size: 13px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        .box p {
            margin: 2px 0;
            font-size: 12px;
        }
 
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
            text-align: center;
        }

        th {
            background: #f2f2f2;
        }

        .right {
            text-align: right;
        }
 
        .total-table {
            margin-top: 10px;
        }

        .total-table td {
            padding: 5px;
        }
 
        .footer {
            margin-top: 15px;
            font-size: 11px;
        }
 
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .sign {
            width: 180px;
            text-align: center;
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
 
@foreach($challans as $challan)

<div class="dc-page">
 
    <div class="header">
        <h2>SMARTSTOCK PRIVATE LIMITED</h2>
        <p>DELIVERY CHALLAN</p>
    </div>

     
    <div class="flex">

        <div class="box">
            <h4>Customer Details</h4>
            <p><b>{{ $challan->customer->name ?? '-' }}</b></p>
            <p>{{ $challan->delivery_to }}</p>
            <p><b>Challan No:</b> {{ $challan->challan_no }}</p>
            <p><b>Date:</b> {{ $challan->challan_date }}</p>
        </div>

        <div class="box">
            <h4>Transport Details</h4>
            <p><b>Mode:</b> {{ $challan->transport_mode }}</p>
            <p><b>Vehicle No:</b> {{ $challan->vehicle_no }}</p>
            <p><b>LR No:</b> {{ $challan->lr_no }}</p>
            <p><b>Dispatch From:</b> {{ $challan->dispatch_from }}</p>
        </div>

    </div>
 
    <table>
        <thead>
            <tr>
                <th>SR</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @php $i = 1; @endphp
            @foreach($challan->items as $item)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->qty }}</td>
                <td>₹ {{ number_format($item->rate,2) }}</td>
                <td>₹ {{ number_format($item->total,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
 
    <table class="total-table">

        <tr>
            <td class="right" style="width:70%"><b>Subtotal</b></td>
            <td>₹ {{ number_format($challan->sub_total, 2) }}</td>
        </tr>

        <tr>
            <td class="right"><b>GST (18%)</b></td>
            <td>₹ {{ number_format($challan->gst_amount, 2) }}</td>
        </tr>

        <tr>
            <td class="right"><b>Grand Total</b></td>
            <td><b>₹ {{ number_format($challan->total_amount, 2) }}</b></td>
        </tr>

    </table>
 
    <div class="footer">
        <p><b>Note:</b> Goods once issued cannot be returned without approval.</p>
        <p>This is a system generated Delivery Challan.</p>
    </div>
 
    <div class="signature">
        <div class="sign">
            ___________________<br>
            Prepared By
        </div>

        <div class="sign">
            ___________________<br>
            Authorized By
        </div>

        <div class="sign">
            ___________________<br>
            Receiver
        </div>
    </div>

</div>

@endforeach

</body>
</html>