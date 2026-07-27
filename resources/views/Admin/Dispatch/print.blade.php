<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Packing Slip - {{ $dispatch->dispatch_no }}</title>

<style>


body{
    font-family:'Segoe UI',Arial,sans-serif;
    font-size:12px;
    color:#222;
    margin:18px;
    line-height:1.25;
}

*{
    box-sizing:border-box;
}

table{
    width:100%;
    border-collapse:collapse;
}

.header{
    border:2px solid #1d3557;
    padding:12px 18px;
    margin-bottom:12px;
}

.logo{
    width:90px;
    text-align:center;
}

.logo img{
    width:70px;
}

.company-name{
    font-size:26px;
    font-weight:700;
    color:#1d3557;
}

.company-info{
    font-size:11px;
    color:#555;
}

.title{
    background:#1d3557;
    color:#fff;
    text-align:center;
    font-size:24px;
    font-weight:bold;
    padding:10px;
    margin:15px 0;
    letter-spacing:2px;
}

.info-box{
    width:49%;
    border:1px solid #cfd8dc;
    vertical-align:top;
    padding:10px;
}

.info-title{
    background:#f4f6f9;
    padding:6px;
    font-weight:600;
    margin:-10px -10px 10px;
    border-bottom:1px solid #ddd;
}

.info-box table td{
    padding:4px;
}

.item-table{
    margin-top:15px;
}

.item-table th{
    background:#1d3557;
    color:#fff;
    padding:8px;
    border:1px solid #ddd;
}

.item-table td{
    padding:8px;
    border:1px solid #ddd;
}

.item-table tbody tr:nth-child(even){
    background:#fafafa;
}

.summary{
    margin-top:15px;
}

.summary td{
    padding:6px;
}

.summary-box{
    width:280px;
    float:right;
}

.summary-box table{
    border:1px solid #ddd;
}

.summary-box td{
    border:1px solid #ddd;
}

.note-box{
    margin-top:18px;
    border:1px solid #ddd;
    padding:10px;
}

.signatures{
    margin-top:45px;
}

.signatures td{
    text-align:center;
    width:25%;
}

.line{
    border-top:1px solid #000;
    margin-top:35px;
    padding-top:6px;
}

.footer{
    margin-top:18px;
    text-align:center;
    font-size:11px;
    color:#666;
}

@media print{

body{
margin:10px;
}

.title{
margin:10px 0;
}

}
</style>

</head>

<body onload="window.print()">

<div class="page">

<!--================ HEADER =================-->

<table class="header">

<tr>

<td width="12%">

<div class="logo">

SS

</div>

</td>

<td width="58%">

<div class="company-info">

<div class="company-name">

SMARTSTOCK PRIVATE LIMITED

</div>

<div class="company-address">

Plot No. 89, MIDC, Vasai, Maharashtra<br>

Phone : +91-7820900557 |
Email : info@smartstock.com<br>

GSTIN : 27XXXXXXXXXXXX

</div>

</div>

</td>

<td width="30%">

<div class="print-title">

<h1>PACKING SLIP</h1>

<span>{{ strtoupper($dispatch->status) }}</span>

</div>

</td>

</tr>

</table>

<!--================ CUSTOMER & DISPATCH =================-->

<table>

<tr>

<td width="52%" style="vertical-align:top;padding-right:8px;">

<div class="card">

<div class="card-header">

SHIP TO

</div>

<div class="card-body">

<div class="bold" style="font-size:16px">

{{ $dispatch->customer->name }}

</div>

<br>

{{ $dispatch->challan->delivery_to }}

<br><br>

<span class="label">GST No :</span>

<span class="value">

{{ $dispatch->customer->gst_no ?? '-' }}

</span>

<br>

<span class="label">Mobile :</span>

<span class="value">

{{ $dispatch->customer->mobile ?? '-' }}

</span>

</div>

</div>

</td>

<td width="48%" style="vertical-align:top;">

<div class="card">

<div class="card-header">

DISPATCH DETAILS

</div>

<div class="card-body">

<table class="info-table">

<tr>

<td width="45%"><b>Dispatch No</b></td>

<td>{{ $dispatch->dispatch_no }}</td>

</tr>

<tr>

<td><b>Dispatch Date</b></td>

<td>{{ date('d-m-Y',strtotime($dispatch->dispatch_date)) }}</td>

</tr>

<tr>

<td><b>Delivery Challan</b></td>

<td>{{ $dispatch->challan->challan_no }}</td>

</tr>

<tr>

<td><b>Transport Mode</b></td>

<td>{{ $dispatch->challan->transport_mode }}</td>

</tr>

<tr>

<td><b>Vehicle No</b></td>

<td>{{ $dispatch->challan->vehicle_no }}</td>

</tr>

<tr>

<td><b>LR No</b></td>

<td>{{ $dispatch->challan->lr_no }}</td>

</tr>

<tr>

<td><b>Prepared By</b></td>

<td>{{ auth()->user()->name ?? 'Admin' }}</td>

</tr>

</table>

</div>

</div>

</td>

</tr>

</table>

<!--================ PRODUCT TABLE START =================-->

<table class="product-table">

<thead>

<tr>

<th width="7%">#</th>

<th>Product</th>

<th width="18%">Dispatch Qty</th>

<th width="18%">Unit</th>

</tr>

</thead>

<tbody>

@php

$totalQty=0;

@endphp

@foreach($dispatch->items as $key=>$item)

@php
$totalQty += $item->quantity;
@endphp

<tr>

    <td class="center">
        {{ $key+1 }}
    </td>

    <td>

        <div style="font-weight:600;font-size:14px;color:#222;">
            {{ $item->product->name }}
        </div>

        <div style="font-size:11px;color:#777;margin-top:4px;">
            Product Code :
            {{ $item->product->product_code ?? 'N/A' }}
        </div>

    </td>

    <td class="center bold">

        {{ number_format($item->quantity,2) }}

    </td>

    <td class="center">

        {{ $item->product->unit ?? 'Nos' }}

    </td>

</tr>

@endforeach

</tbody>

</table>

<br><br>

<table>

<tr>

<td width="62%" style="vertical-align:top;padding-right:15px;">


<div class="card">

<div class="card-header">

Remarks

</div>

<div class="card-body" style="min-height:120px;">

{{ $dispatch->remarks ?? 'No Remarks' }}

</div>

</div>

</td>

<td width="38%">


<table class="summary-box">

<tr>

<td>

<b>Total Products</b>

</td>

<td class="right">

{{ $dispatch->items->count() }}

</td>

</tr>

<tr>

<td>

<b>Total Quantity</b>

</td>

<td class="right">

{{ number_format($totalQty,2) }}

</td>

</tr>

<tr>

<td>

<b>Dispatch Status</b>

</td>

<td class="right">

<span style="color:#0d6efd;font-weight:bold;">

{{ strtoupper($dispatch->status) }}

</span>

</td>

</tr>

<tr>

<td>

<b>Dispatch Date</b>

</td>

<td class="right">

{{ date('d-m-Y',strtotime($dispatch->dispatch_date)) }}

</td>

</tr>

</table>

</td>

</tr>

</table>

<!--==============================-->

<div class="footer">

<table class="signature">

<tr>

<td width="25%">

<div class="signature-line">

Prepared By

</div>

</td>

<td width="25%">

<div class="signature-line">

Checked By

</div>

</td>

<td width="25%">

<div class="signature-line">

Store Incharge

</div>

</td>

<td width="25%">

<div class="signature-line">

Receiver Signature

</div>

</td>

</tr>

</table>

</div>

<div class="footer-note">

<table width="100%">

<tr>

<td align="left">

Generated by <b>SmartStock ERP</b>

</td>

<td align="center">

Printed :
{{ now()->format('d-m-Y h:i A') }}

</td>

<td align="right">

Dispatch No :
<b>{{ $dispatch->dispatch_no }}</b>

</td>

</tr>

</table>

</div>

</div>

</body>

</html>