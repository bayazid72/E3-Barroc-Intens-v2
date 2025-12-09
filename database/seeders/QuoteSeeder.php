<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\Company;
use App\Models\Product;
use App\Models\InvoiceLine;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $products = Product::all();

        if ($companies->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Geen bedrijven of producten gevonden. Run eerst CompanySeeder en ProductSeeder.');
            return;
        }

        // Create 10 quotes
        for ($i = 0; $i < 10; $i++) {
            $quote = Quote::create([
                'company_id' => $companies->random()->id,
                'invoice_date' => now()->subDays(rand(1, 60)),
                'total_amount' => 0, // Will be calculated
                'status' => collect(['draft', 'sent', 'accepted', 'open'])->random(),
                'type' => 'quote',
                'is_sent' => rand(0, 1),
            ]);

            // Add 2-5 quote lines
            $totalAmount = 0;
            $numberOfLines = rand(2, 5);
            
            for ($j = 0; $j < $numberOfLines; $j++) {
                $product = $products->random();
                $amount = rand(1, 10);
                $price = $product->price;
                
                InvoiceLine::create([
                    'invoice_id' => $quote->id,
                    'product_id' => $product->id,
                    'amount' => $amount,
                    'price_snapshot' => $price,
                ]);

                $totalAmount += $amount * $price;
            }

            // Update total amount
            $quote->update(['total_amount' => $totalAmount]);
        }

        $this->command->info('10 offertes aangemaakt met bijbehorende regels.');
    }
}
