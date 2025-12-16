<?php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

class QuoteOverview extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $statusFilter = '';

    public function render()
    {
        return view('livewire.quote-overview', [
            'quotes' => Invoice::withoutGlobalScope('invoice')
                ->where('type', 'quote')
                ->when($this->statusFilter, function ($q) {
                    if ($this->statusFilter === 'sent') {
                        $q->where('is_sent', true);
                    }
                    if ($this->statusFilter === 'concept') {
                        $q->where('is_sent', false);
                    }
                })
                ->with('company')
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ]);
    }
}
