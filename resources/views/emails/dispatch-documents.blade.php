<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Dispatch Documents</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color:#333;">

    <h2>Dear {{ $dispatch->customer->company_name ?? $dispatch->customer->name }},</h2>

    <p>
        Greetings from <strong>Radcom Packaging Pvt. Ltd.</strong>
    </p>

    <p>
        Your order has been successfully dispatched.
        Please find the attached documents for your reference.
    </p>

    <table cellpadding="8" cellspacing="0" border="1" width="600"
        style="border-collapse: collapse;">

        <tr style="background:#f2f2f2;">
            <th align="left">Dispatch No.</th>
            <td>{{ $dispatch->dispatch_no }}</td>
        </tr>

        <tr>
            <th align="left">Invoice No.</th>
            <td>{{ $invoice->invoice_no }}</td>
        </tr>

        <tr style="background:#f2f2f2;">
            <th align="left">Dispatch Date</th>
            <td>{{ \Carbon\Carbon::parse($dispatch->dispatch_date)->format('d-m-Y') }}</td>
        </tr>

        <tr>
            <th align="left">Customer</th>
            <td>{{ $dispatch->customer->company_name ?? $dispatch->customer->name }}</td>
        </tr>

        <tr style="background:#f2f2f2;">
            <th align="left">Total Amount</th>
            <td>₹ {{ number_format($invoice->total_amount,2) }}</td>
        </tr>

    </table>

    <br>

    <p>
        Attached Documents:
    </p>

    <ul>
        <li>✔ Packing Slip</li>
        <li>✔ Tax Invoice</li>
    </ul>

    <br>

    <p>
        If you have any questions, please feel free to contact us.
    </p>

    <br>

    <p>
        Thanks & Regards,<br>

        <strong>Radcom Packaging Pvt. Ltd.</strong><br>

        Email :
        manasinikam09@gmail.com<br>

        Website :
        https://radcompackaging.com
    </p>

</body>

</html>