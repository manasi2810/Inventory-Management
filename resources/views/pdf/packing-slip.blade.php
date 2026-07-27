<h2>Packing Slip</h2>

<p><b>Dispatch No:</b> {{ $dispatch->dispatch_no }}</p>
<p><b>Customer:</b> {{ $dispatch->customer->name }}</p>
<p><b>Date:</b> {{ $dispatch->dispatch_date }}</p>

<hr>

<table border="1" width="100%" cellpadding="5">
    <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Rate</th>
        <th>Amount</th>
    </tr>

    @foreach($dispatch->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->rate }}</td>
            <td>{{ $item->amount }}</td>
        </tr>
    @endforeach
</table>