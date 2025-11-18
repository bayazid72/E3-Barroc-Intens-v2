<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role_id;

    public $userIdBeingEdited = null;
    public $confirmingUserDeletion = null;

    protected $rules = [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email',
        'password' => 'nullable|string|min:8|same:password_confirmation',
        'role_id'  => 'required|exists:roles,id',
    ];

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        
    }

    public function updating()
    {
        $this->resetErrorBag();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role_id = '';
        $this->userIdBeingEdited = null;
    }

    public function createUser()
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|same:password_confirmation',
            'role_id'  => 'required|exists:roles,id',
        ]);

        User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'role_id'  => $this->role_id,
        ]);

        $this->resetForm();
        session()->flash('success', 'Gebruiker aangemaakt.');
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);

        $this->userIdBeingEdited = $user->id;
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->role_id = $user->role_id;

        $this->password = '';
        $this->password_confirmation = '';
    }

    public function updateUser()
    {
        $user = User::findOrFail($this->userIdBeingEdited);

        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|same:password_confirmation',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $data = [
            'name'    => $this->name,
            'email'   => $this->email,
            'role_id' => $this->role_id,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->resetForm();
        session()->flash('success', 'Gebruiker bijgewerkt.');
    }

    public function confirmDelete($userId)
    {
        $this->confirmingUserDeletion = $userId;
    }

    public function deleteUser()
    {
        $user = User::findOrFail($this->confirmingUserDeletion);

        if (auth()->id() === $user->id) {
            session()->flash('error', 'Je kunt jezelf niet verwijderen.');
            $this->confirmingUserDeletion = null;
            return;
        }

        $user->delete();
        $this->confirmingUserDeletion = null;

        session()->flash('success', 'Gebruiker verwijderd.');
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::with('role')->orderBy('name')->paginate(10),
            'roles' => Role::all(),
        ]);
    }
}
