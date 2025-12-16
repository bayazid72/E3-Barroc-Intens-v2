<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $name;
    public $phone;
    public $street;
    public $house_number;
    public $city;
    public $country_code;
    public $bkr_checked = false;
    public $contact_id;

    public $companyIdBeingEdited = null;
    public $confirmingCompanyDeletion = null;

    public $searchInput = '';
    public $search = '';

    protected $rules = [
        'name'         => 'required|string|max:255',
        'phone'        => 'nullable|string|max:50',
        'street'       => 'nullable|string|max:255',
        'house_number' => 'nullable|string|max:50',
        'city'         => 'nullable|string|max:255',
        'country_code' => 'required|string|max:3',
        'contact_id'   => 'nullable|exists:users,id',
        'bkr_checked'  => 'boolean',
    ];

    public function updating()
    {
        $this->resetErrorBag();
    }

    public function resetForm()
    {
        $this->reset([
            'name','phone','street','house_number',
            'city','country_code','contact_id',
            'bkr_checked','companyIdBeingEdited'
        ]);
    }

    public function createCompany()
    {
        $this->validate();

        Company::create([
            'name'         => $this->name,
            'phone'        => $this->phone,
            'street'       => $this->street,
            'house_number' => $this->house_number,
            'city'         => $this->city,
            'country_code' => $this->country_code,
            'contact_id'   => $this->contact_id,
            'bkr_checked'  => $this->bkr_checked,
        ]);

        $this->resetForm();
        session()->flash('success', 'Bedrijf aangemaakt.');
    }

    public function editCompany($id)
    {
        $company = Company::findOrFail($id);

        $this->companyIdBeingEdited = $company->id;
        $this->name         = $company->name;
        $this->phone        = $company->phone;
        $this->street       = $company->street;
        $this->house_number = $company->house_number;
        $this->city         = $company->city;
        $this->country_code = $company->country_code;
        $this->contact_id   = $company->contact_id;
        $this->bkr_checked  = $company->bkr_checked;

        $this->dispatch('scrollToTop');
    }

    public function updateCompany()
    {
        $company = Company::findOrFail($this->companyIdBeingEdited);

        $this->validate();

        $company->update([
            'name'         => $this->name,
            'phone'        => $this->phone,
            'street'       => $this->street,
            'house_number' => $this->house_number,
            'city'         => $this->city,
            'country_code' => $this->country_code,
            'contact_id'   => $this->contact_id,
            'bkr_checked'  => $this->bkr_checked,
        ]);

        $this->resetForm();
        session()->flash('success', 'Bedrijf bijgewerkt.');
    }

    public function confirmDelete($id)
    {
        $this->confirmingCompanyDeletion = $id;
    }

    public function deleteCompany()
    {
        Company::findOrFail($this->confirmingCompanyDeletion)->delete();
        $this->confirmingCompanyDeletion = null;

        session()->flash('success', 'Bedrijf verwijderd.');
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
        return view('livewire.company-management', [
            'companies' => Company::with('contact')
                ->when($this->search, fn ($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                )
                ->orderBy('name')
                ->paginate(10),

            'users' => User::orderBy('name')->get(),
        ]);
    }
}
