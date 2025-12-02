<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\InvoiceLine;

// Find invoice 31
$invoice = Invoice::with('lines')->find(31);

if (!$invoice) {
    echo "Invoice niet gevonden!\n";
    exit(1);
}

echo "Huidige invoice lines:\n";
foreach ($invoice->lines as $line) {
    $lineTotal = $line->amount * $line->price_snapshot;
    echo "- {$line->product->name}: {$line->amount} x €{$line->price_snapshot} = €{$lineTotal}\n";
}

// Recalculate total based on actual lines
$calculatedTotal = $invoice->lines->sum(function($line) {
    return $line->amount * $line->price_snapshot;
});

echo "\nBerekend totaal (excl. BTW): €" . number_format($calculatedTotal, 2) . "\n";
echo "BTW (21%): €" . number_format($calculatedTotal * 0.21, 2) . "\n";
echo "Totaal (incl. BTW): €" . number_format($calculatedTotal * 1.21, 2) . "\n";

echo "\nOud factuur totaal: €" . number_format($invoice->total_amount, 2) . "\n";

// Update invoice total to match calculated amount (including VAT)
$newTotal = $calculatedTotal * 1.21;
$invoice->update(['total_amount' => $newTotal]);

echo "Nieuw factuur totaal: €" . number_format($newTotal, 2) . "\n";
