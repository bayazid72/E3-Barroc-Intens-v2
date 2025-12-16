<?php

namespace App\Livewire;

use App\Models\Note;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class NoteManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Form fields
    public $note;
    public $type = 'note';
    public $follow_up_at;
    public $company_id;

    // State
    public $noteIdBeingEdited = null;
    public $confirmingNoteDeletion = null;

    // Search
    public $searchInput = '';
    public $search = '';

    protected $rules = [
        'note'         => 'required|string',
        'company_id'   => 'required|exists:companies,id',
        'type'         => 'required|in:note,afspraak,klacht',
        'follow_up_at' => 'nullable|date',
    ];

    public function updating()
    {
        $this->resetErrorBag();
    }

    public function resetForm()
    {
        $this->note = '';
        $this->type = 'note';
        $this->follow_up_at = null;
        $this->company_id = '';
        $this->noteIdBeingEdited = null;
    }

    public function createNote()
    {
        $this->validate();

        Note::create([
            'note'         => $this->note,
            'type'         => $this->type,
            'follow_up_at' => $this->follow_up_at,
            'company_id'   => $this->company_id,
            'author_id'    => Auth::id(),
            'date'         => Carbon::now(),
        ]);

        $this->resetForm();
        session()->flash('success', 'Notitie aangemaakt.');
    }

    public function editNote($id)
    {
        $note = Note::findOrFail($id);

        $this->noteIdBeingEdited = $note->id;
        $this->note         = $note->note;
        $this->type         = $note->type;
        $this->follow_up_at = optional($note->follow_up_at)->format('Y-m-d\TH:i');
        $this->company_id   = $note->company_id;

        $this->dispatch('scrollToTop');
    }

    public function updateNote()
    {
        $note = Note::findOrFail($this->noteIdBeingEdited);

        $this->validate();

        $note->update([
            'note'         => $this->note,
            'type'         => $this->type,
            'follow_up_at' => $this->follow_up_at,
            'company_id'   => $this->company_id,
        ]);

        $this->resetForm();
        session()->flash('success', 'Notitie bijgewerkt.');
    }

    public function confirmDelete($id)
    {
        $this->confirmingNoteDeletion = $id;
    }

    public function deleteNote()
    {
        Note::findOrFail($this->confirmingNoteDeletion)->delete();
        $this->confirmingNoteDeletion = null;

        session()->flash('success', 'Notitie verwijderd.');
    }

    public function applyFilters()
    {
        $this->search = $this->searchInput;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->searchInput = '';
        $this->resetPage();
    }


    public function render()
    {
        return view('livewire.note-management', [
            'notes' => Note::with(['company', 'author'])
                ->when($this->search, fn ($q) =>
                    $q->where('note', 'like', "%{$this->search}%")
                )
                ->latest()
                ->paginate(10),

            'companies' => Company::orderBy('name')->get(),
        ]);
    }
}
