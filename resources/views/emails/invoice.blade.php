<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px 20px;
        }
        .invoice-details {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #2563eb;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Barroc Intens</h1>
            <p>Uw factuur is klaar</p>
        </div>
        
        <div class="content">
            <p>Beste {{ $invoice->company->name }},</p>
            
            <p>Hartelijk dank voor uw vertrouwen in Barroc Intens. Bijgevoegd vindt u de factuur voor onze diensten.</p>
            
            <div class="invoice-details">
                <h3 style="margin-top: 0; color: #2563eb;">Factuurgegevens</h3>
                <table style="width: 100%;">
                    <tr>
                        <td><strong>Factuurnummer:</strong></td>
                        <td>{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td><strong>Factuurdatum:</strong></td>
                        <td>{{ $invoice->invoice_date?->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Vervaldatum:</strong></td>
                        <td>{{ $invoice->invoice_date?->addDays(30)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Totaalbedrag:</strong></td>
                        <td style="font-size: 18px; color: #2563eb;"><strong>€{{ number_format($invoice->total_amount, 2, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
            
            <div style="background-color: white; padding: 20px; border-radius: 5px;">
                <h3 style="margin-top: 0;">Betaalinformatie</h3>
                <p>Gelieve het totaalbedrag voor {{ $invoice->invoice_date?->addDays(30)->format('d-m-Y') }} over te maken naar:</p>
                <p>
                    <strong>IBAN:</strong> NL12 ABNA 0123 4567 89<br>
                    <strong>T.n.v.:</strong> Barroc Intens<br>
                    <strong>O.v.v.:</strong> {{ $invoice->invoice_number }}
                </p>
            </div>
            
            <p>De volledige factuur vindt u als bijlage bij deze email.</p>
            
            <p>Heeft u vragen over deze factuur? Neem gerust contact met ons op via info@barrocintens.nl of 020-1234567.</p>
            
            <p>Met vriendelijke groet,<br>
            <strong>Team Barroc Intens</strong></p>
        </div>
        
        <div class="footer">
            <p>
                Barroc Intens<br>
                Schoolstraat 123, 1234 AB Amsterdam<br>
                KVK: 12345678 | BTW: NL123456789B01<br>
                Tel: 020-1234567 | Email: info@barrocintens.nl
            </p>
        </div>
    </div>
</body>
</html>
