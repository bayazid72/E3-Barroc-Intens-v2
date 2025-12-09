<?php

namespace App\Livewire\Finance;

use App\Models\Quote;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class QuoteList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    #[On('quote-converted')]
    public function refreshList()
    {
        // Refresh the list after conversion
        $this->resetPage();
    }

    public function render()
    {
        $quotes = Quote::with(['company', 'lines'])
            ->when($this->search, function ($query) {
                $query->whereHas('company', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.finance.quote-list', [
            'quotes' => $quotes,
        ]);
    }
}
