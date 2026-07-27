<!DOCTYPE html>
<html>
<head>
<style>

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    margin: 0;
    color: #111;
}

/* MAIN PAGE */
.page {
    padding: 18px;
}

/* HEADER BAR */
.header {
    background: #1f2937;
    color: #fff;
    padding: 15px;
    text-align: center;
}

.company {
    font-size: 20px;
    font-weight: bold;
}

.sub {
    font-size: 11px;
    margin-top: 4px;
    color: #e5e7eb;
}

.invoice-title {
    margin-top: 5px;
    font-size: 13px;
    font-weight: bold;
    letter-spacing: 1px;
}

/* SECTION BOX GRID */
.grid {
    width: 100%;
    margin-top: 12px;
    border-collapse: collapse;
}

.box {
    width: 50%;
    vertical-align: top;
    border: 1px solid #000;
    padding: 10px;
}

.box-title {
    font-weight: bold;
    margin-bottom: 6px;
    border-bottom: 1px solid #000;
    padding-bottom: 3px;
}

/* INVOICE INFO BAR */
.info-bar {
    width: 100%;
    border: 1px solid #000;
    margin-top: 10px;
}

.info-bar td {
    padding: 6px;
    font-size: 11px;
}

/* TABLE */
table.items {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}

.items th {
    background: #111827;
    color: #fff;
    padding: 7px;
    font-size: 11px;
}

.items td {
    border: 1px solid #000;
    padding: 6px;
    font-size: 11px;
}

.text-right { text-align: right; }
.text-center { text-align: center; }

/* TOTAL BOX */
.total-box {
    width: 40%;
    margin-left: auto;
    margin-top: 10px;
    border: 1px solid #000;
}

.total-box td {
    padding: 6px;
    font-size: 12px;
}

.grand {
    font-weight: bold;
    font-size: 13px;
}

/* FOOTER */
.footer {
    margin-top: 40px;
    display: flex;
    justify-content: space-between;
}

.sign {
    text-align: right;
}

.words {
    font-style: italic;
}

</style>
</head>

<body>

<div class="page">

{{-- HEADER --}}
<div class="header">

    <div class="company">SMARTSTOCK PRIVATE LIMITED</div>

    <div class="sub">
        GSTIN: 27ABCDE1234F1Z5 | MIDC Phase 3, Pune, Maharashtra - 411001
    </div>

    <div class="invoice-title">TAX INVOICE</div>

</div>

{{-- PARTY DETAILS --}}
<table class="grid">

<tr>

<td class="box">

<div class="box-title">Seller</div>

SMARTSTOCK PRIVATE LIMITED<br>
MIDC Phase 3, Pune<br>
GSTIN: 27ABCDE1234F1Z5

</td>

<td class="box">

<div class="box-title">Bill To / Ship To</div>

<strong>{{ $invoice->customer->name ?? 'Neha Joshi' }}</strong><br>
Vasai, Mumbai<br>
Maharashtra, India<br>
GSTIN: 27CUST9876M1Z9

</td>

</tr>

</table>

{{-- INVOICE INFO --}}
<table class="info-bar">

<tr>
<td><b>Invoice No:</b> {{ $invoice->invoice_no }}</td>
<td><b>Date:</b> {{ $invoice->invoice_date }}</td>
<td><b>Dispatch:</b> {{ $invoice->dispatch->dispatch_no ?? '-' }}</td>
<td><b>Payment:</b> Immediate</td>
</tr>

</table>

{{-- ITEMS --}}
<table class="items">

<thead>
<tr>
    <th>#</th>
    <th>Product</th>
    <th>Qty</th>
    <th>Rate</th>
    <th>Taxable</th>
    <th>GST %</th>
    <th>GST Amt</th>
    <th>Total</th>
</tr>
</thead>

<tbody>

@foreach($invoice->items as $key => $item)

@php
$taxable = $item->quantity * $item->rate;
@endphp

<tr>
    <td class="text-center">{{ $key+1 }}</td>
    <td>{{ $item->product->name ?? '-' }}</td>
    <td class="text-center">{{ $item->quantity }}</td>
    <td class="text-right">{{ number_format($item->rate,2) }}</td>
    <td class="text-right">{{ number_format($taxable,2) }}</td>
    <td class="text-center">{{ $item->gst_percent }}%</td>
    <td class="text-right">{{ number_format($item->gst_amount,2) }}</td>
    <td class="text-right">{{ number_format($item->amount,2) }}</td>
</tr>

@endforeach

</tbody>

</table>

{{-- TOTAL --}}
<table class="total-box">

<tr>
<td>Sub Total</td>
<td class="text-right">₹ {{ number_format($invoice->sub_total,2) }}</td>
</tr>

<tr>
<td>GST</td>
<td class="text-right">₹ {{ number_format($invoice->gst_amount,2) }}</td>
</tr>

<tr class="grand">
<td>Total</td>
<td class="text-right">₹ {{ number_format($invoice->total_amount,2) }}</td>
</tr>

</table>

{{-- FOOTER --}}
<div class="footer">

<div class="words">
<strong>Amount in Words:</strong><br>
Twenty Five Thousand Four Hundred Twenty Nine Only
</div>

<div class="sign">
For SMARTSTOCK PRIVATE LIMITED<br><br><br>
Authorized Signatory
</div>

</div>

</div>

</body>
</html>