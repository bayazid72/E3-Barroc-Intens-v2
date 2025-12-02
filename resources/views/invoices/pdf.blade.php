<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            text-align: right;
            margin-top: -60px;
        }
        .info-box {
            background-color: #f3f4f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #4b5563;
            font-size: 10px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #e5e7eb;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #9ca3af;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .total-row {
            padding: 8px 0;
            display: flex;
            justify-content: space-between;
        }
        .total-row.grand {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #2563eb;
            padding-top: 12px;
            color: #2563eb;
        }
        .payment-info {
            margin-top: 100px;
            padding: 15px;
            background-color: #f0f9ff;
            border-left: 4px solid #2563eb;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">Barroc Intens</div>
        <div style="font-size: 11px; color: #6b7280;">
            Schoolstraat 123<br>
            1234 AB Amsterdam<br>
            KVK: 12345678<br>
            BTW: NL123456789B01<br>
            Tel: 020-1234567 | Email: info@barrocintens.nl
        </div>
    </div>

    <div class="invoice-title">FACTUUR</div>

    <!-- Invoice Info -->
    <div style="margin-top: 30px; display: table; width: 100%;">
        <div style="display: table-cell; width: 50%;">
            <div class="info-box">
                <div class="info-label">Factuur aan</div>
                <strong>{{ $invoice->company->name }}</strong><br>
                {{ $invoice->company->address ?? '' }}<br>
                {{ $invoice->company->postal_code ?? '' }} {{ $invoice->company->city ?? '' }}<br>
                @if($invoice->company->email)
                    Email: {{ $invoice->company->email }}
                @endif
            </div>
        </div>
        <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 20px;">
            <div class="info-box">
                <div class="info-label">Factuurgegevens</div>
                <strong>Factuurnummer:</strong> {{ $invoice->invoice_number }}<br>
                <strong>Factuurdatum:</strong> {{ $invoice->invoice_date?->format('d-m-Y') }}<br>
                <strong>Vervaldatum:</strong> {{ $invoice->invoice_date?->addDays(30)->format('d-m-Y') }}<br>
                @if($invoice->contract_id)
                    <strong>Contract ID:</strong> {{ $invoice->contract_id }}
                @endif
            </div>
        </div>
    </div>

    <!-- Invoice Lines -->
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Omschrijving</th>
                <th class="text-right" style="width: 10%;">Aantal</th>
                <th class="text-right" style="width: 20%;">Prijs per stuk</th>
                <th class="text-right" style="width: 20%;">Totaal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $line)
                <tr>
                    <td>
                        <strong>{{ $line->product->name ?? $line->description }}</strong>
                        @if($line->description && $line->product)
                            <br><span style="font-size: 10px; color: #6b7280;">{{ $line->description }}</span>
                        @endif
                    </td>
                    <td class="text-right">{{ $line->quantity }}</td>
                    <td class="text-right">€{{ number_format($line->unit_price, 2, ',', '.') }}</td>
                    <td class="text-right"><strong>€{{ number_format($line->total_price, 2, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <div class="total-row">
            <span>Subtotaal:</span>
            <span>€{{ number_format($invoice->total_amount / 1.21, 2, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>BTW (21%):</span>
            <span>€{{ number_format($invoice->total_amount - ($invoice->total_amount / 1.21), 2, ',', '.') }}</span>
        </div>
        <div class="total-row grand">
            <span>Totaal:</span>
            <span>€{{ number_format($invoice->total_amount, 2, ',', '.') }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <!-- Payment Info -->
    <div class="payment-info">
        <strong style="font-size: 14px;">Betaalinformatie</strong><br><br>
        Gelieve het totaalbedrag van <strong>€{{ number_format($invoice->total_amount, 2, ',', '.') }}</strong> 
        voor <strong>{{ $invoice->invoice_date?->addDays(30)->format('d-m-Y') }}</strong> over te maken naar:<br><br>
        <strong>IBAN:</strong> NL12 ABNA 0123 4567 89<br>
        <strong>T.n.v.:</strong> Barroc Intens<br>
        <strong>O.v.v.:</strong> {{ $invoice->invoice_number }}
    </div>

    <!-- Footer -->
    <div class="footer">
        Barroc Intens | KVK: 12345678 | BTW: NL123456789B01<br>
        Schoolstraat 123, 1234 AB Amsterdam | Tel: 020-1234567 | info@barrocintens.nl
    </div>
</body>
</html>
