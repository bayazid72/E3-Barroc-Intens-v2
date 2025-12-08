<?php

namespace App\Livewire\Finance;

use App\Models\Quote;
use Livewire\Component;

class QuoteShow extends Component
{
    public Quote $quote;

    public function mount(Quote $quote)
    {
        $this->quote = $quote->load(['company', 'contract', 'lines.product']);
    }

    public function render()
    {
        return view('livewire.finance.quote-show');
    }
}
