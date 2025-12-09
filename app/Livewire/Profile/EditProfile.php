<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;

class EditProfile extends Component
{
    public $name;
    public $email;
    public $password;
    public $role;
    public $section;
    public $isOpen = false;

    protected $listeners = ['open-profile-modal' => 'openModal'];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role === 'admin' || $user->role === 'super_admin' ? 'Administrateur / Gérant' : 'Employé';
        $this->section = $user->section ? $user->section->nom : 'Aucune (Accès Global ou Limité)';
        $this->password = '';
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->mount();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->mount();
    }

    public function save()
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ];

        if (!empty($this->password)) {
            $rules['password'] = ['required', 'min:8'];
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (!empty($this->password)) {
            $data['password'] = bcrypt($this->password);
        }

        $user->update($data);

        session()->flash('message', 'Profil mis à jour avec succès.');

        $this->dispatch('profile-updated');
        $this->closeModal();

        $this->redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.profile.edit-profile');
    }
}
