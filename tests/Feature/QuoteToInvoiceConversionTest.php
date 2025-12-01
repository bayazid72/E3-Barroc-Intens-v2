<?php

use App\Models\Quote;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\InvoiceLine;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test data
    $this->company = Company::create([
        'name' => 'Test Company',
        'email' => 'test@company.com',
        'phone' => '0612345678',
        'country_code' => 'NL',
        'address' => 'Test Street 1',
        'city' => 'Amsterdam',
        'postal_code' => '1000AA',
    ]);

    $category = ProductCategory::create([
        'name' => 'Test Category',
    ]);

    $this->product = Product::create([
        'name' => 'Test Product',
        'product_category_id' => $category->id,
        'price' => 100.00,
        'stock' => 10,
    ]);
});

test('quote can be converted to invoice', function () {
    // Create a quote with lines
    $quote = Quote::create([
        'company_id' => $this->company->id,
        'invoice_date' => now(),
        'total_amount' => 200.00,
        'status' => 'accepted',
        'type' => 'quote',
    ]);

    InvoiceLine::create([
        'invoice_id' => $quote->id,
        'product_id' => $this->product->id,
        'amount' => 2,
        'price_snapshot' => 100.00,
    ]);

    // Convert to invoice
    $invoice = $quote->convertToInvoice();

    // Assert invoice was created
    expect($invoice)->toBeInstanceOf(Invoice::class);
    expect($invoice->type)->toBe('invoice');
    expect($invoice->company_id)->toBe($quote->company_id);
    expect($invoice->total_amount)->toBe($quote->total_amount);
    expect($invoice->invoice_number)->not->toBeNull();

    // Assert invoice lines were copied
    expect($invoice->lines)->toHaveCount(1);
    expect($invoice->lines->first()->product_id)->toBe($this->product->id);
    expect($invoice->lines->first()->amount)->toBe(2);

    // Assert quote status was updated
    $quote->refresh();
    expect($quote->status)->toBe('converted');
});

test('quote with no lines cannot be converted', function () {
    $quote = Quote::create([
        'company_id' => $this->company->id,
        'invoice_date' => now(),
        'total_amount' => 0,
        'status' => 'draft',
        'type' => 'quote',
    ]);

    expect($quote->canBeConverted())->toBeFalse();
});

test('already converted quote cannot be converted again', function () {
    $quote = Quote::create([
        'company_id' => $this->company->id,
        'invoice_date' => now(),
        'total_amount' => 100.00,
        'status' => 'converted',
        'type' => 'quote',
    ]);

    InvoiceLine::create([
        'invoice_id' => $quote->id,
        'product_id' => $this->product->id,
        'amount' => 1,
        'price_snapshot' => 100.00,
    ]);

    expect($quote->canBeConverted())->toBeFalse();
});

test('invoice number is auto generated', function () {
    $invoice = Invoice::create([
        'company_id' => $this->company->id,
        'invoice_date' => now(),
        'total_amount' => 100.00,
        'status' => 'open',
        'type' => 'invoice',
    ]);

    expect($invoice->invoice_number)->not->toBeNull();
    expect($invoice->invoice_number)->toMatch('/^INV-\d{4}-\d{4}$/');
});

test('invoice number increments correctly', function () {
    $invoice1 = Invoice::create([
        'company_id' => $this->company->id,
        'invoice_date' => now(),
        'total_amount' => 100.00,
        'status' => 'open',
        'type' => 'invoice',
    ]);

    $invoice2 = Invoice::create([
        'company_id' => $this->company->id,
        'invoice_date' => now(),
        'total_amount' => 200.00,
        'status' => 'open',
        'type' => 'invoice',
    ]);

    expect($invoice2->invoice_number)->not->toBe($invoice1->invoice_number);
    
    // Extract numbers and verify increment
    preg_match('/INV-\d{4}-(\d{4})/', $invoice1->invoice_number, $matches1);
    preg_match('/INV-\d{4}-(\d{4})/', $invoice2->invoice_number, $matches2);
    
    expect((int)$matches2[1])->toBe((int)$matches1[1] + 1);
});
