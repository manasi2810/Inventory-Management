<!DOCTYPE html>
<html>
<head>
    <title>Delivery Challan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 25px;
            color: #000;
        }

         
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
        }

        .header p {
            margin: 3px 0;
            font-size: 12px;
        }

        
        .flex {
            display: flex;
            margin-top: 10px;
        }

        .col {
            width: 50%;
            padding: 0 10px;
        }

        .col:first-child {
            border-right: 1px solid #000;
        }

        .col p {
            margin: 4px 0;
            font-size: 12px;
        }

        .col h4 {
            margin: 0 0 8px;
            font-size: 13px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }

         
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #000;
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

         
        .totals {
            width: 100%;
            margin-top: 10px;
        }

        .totals td {
            border: none;
            padding: 4px;
        }
 
        .footer {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
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
<div class="header">
    <h2>SMARTSTOCK PRIVATE LIMITED</h2>
    <p>DELIVERY CHALLAN</p>
</div> 
<div class="flex"> 
    <div class="col">
        <h4>Customer Details</h4>
        <p><b>{{ $challan->customer->name ?? '-' }}</b></p>
        <p>{{ $challan->delivery_to ?? '-' }}</p>
        <p><b>Challan No:</b> {{ $challan->challan_no ?? '-' }}</p>
        <p><b>Date:</b> {{ $challan->challan_date ? date('d-m-Y', strtotime($challan->challan_date)) : '-' }}</p>
    </div> 
    <div class="col">
        <h4>Transport Details</h4>
        <p><b>Mode:</b> {{ $challan->transport_mode ?? '-' }}</p>
        <p><b>Vehicle No:</b> {{ $challan->vehicle_no ?? '-' }}</p>
        <p><b>LR No:</b> {{ $challan->lr_no ?? '-' }}</p>
        <p><b>Dispatch From:</b> {{ $challan->dispatch_from ?? '-' }}</p>
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
            <td>{{ $item->qty ?? 0 }}</td>
            <td>₹ {{ number_format($item->rate ?? 0, 2) }}</td>
            <td>₹ {{ number_format($item->total ?? 0, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table> 
<table class="totals"> 
    <tr>
        <td class="right" style="width:70%;"><b>Subtotal (Taxable Value)</b></td>
        <td class="right">₹ {{ number_format($challan->sub_total ?? 0, 2) }}</td>
    </tr> 
    <tr>
        <td class="right"><b>GST Amount</b></td>
        <td class="right">₹ {{ number_format($challan->gst_amount ?? 0, 2) }}</td>
    </tr> 
    <tr>
        <td class="right"><b>Transport / Other Charges</b></td>
        <td class="right">₹ {{ number_format($challan->other_charges ?? 0, 2) }}</td>
    </tr> 
    <tr>
        <td class="right"><b>Round Off</b></td>
        <td class="right">₹ {{ number_format($challan->round_off ?? 0, 2) }}</td>
    </tr> 
    <tr>
        <td class="right" style="border-top:2px solid #000;"><b>Grand Total</b></td>
        <td class="right" style="border-top:2px solid #000;">
            <b>₹ {{ number_format($challan->total_amount ?? 0, 2) }}</b>
        </td>
    </tr> 
</table> 
<div class="footer">
    <p><b>Note:</b> Goods once issued cannot be returned without approval.</p>
    <p>This is a computer generated Delivery Challan.</p>
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
</body>
</html>