<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ManagerManager extends Component
{
    public $managers;
    public $etablissements;

    // Form properties
    public $name;
    public $email;
    public $password;
    public $assignedEtablissementIds = [];

    public $selectedManagerId;
    public $isEditing = false;
    public $isOpen = false;

    public $search = '';

    protected function rules(): array
    {
        $emailRule = $this->isEditing
            ? 'required|email|max:255|unique:users,email,' . $this->selectedManagerId
            : 'required|email|max:255|unique:users,email';

        return [
            'name'                      => 'required|string|max:255',
            'email'                     => $emailRule,
            'password'                  => $this->isEditing ? 'nullable|min:8' : 'required|min:8',
            'assignedEtablissementIds'  => 'nullable|array',
            'assignedEtablissementIds.*' => 'exists:etablissements,id',
        ];
    }

    public function mount()
    {
        if (!Auth::user()->isSuperAdmin()) {
            return redirect()->route('super_admin.dashboard');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $query = User::with('ownedEstablishments')
            ->where('role', 'manager')
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $this->managers = $query->get();
        $this->etablissements = Etablissement::orderBy('nom')->get();
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $manager = User::with('ownedEstablishments')->find($id);

        if ($manager && $manager->role === 'manager') {
            $this->selectedManagerId = $id;
            $this->name = $manager->name;
            $this->email = $manager->email;
            $this->password = '';
            $this->assignedEtablissementIds = $manager->ownedEstablishments->pluck('id')->toArray();
            $this->isEditing = true;
            $this->isOpen = true;
        }
    }

    public function save()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $this->validate();

        if ($this->isEditing) {
            $manager = User::find($this->selectedManagerId);

            if ($manager && $manager->role === 'manager') {
                $data = [
                    'name'  => $this->name,
                    'email' => $this->email,
                ];

                if ($this->password) {
                    $data['password'] = Hash::make($this->password);
                }

                $manager->update($data);

                // Re-assign establishments: remove old manager_id links, add new ones
                Etablissement::where('manager_id', $manager->id)->update(['manager_id' => null]);
                if (!empty($this->assignedEtablissementIds)) {
                    Etablissement::whereIn('id', $this->assignedEtablissementIds)
                        ->update(['manager_id' => $manager->id]);
                }

                session()->flash('message', 'Manager mis à jour avec succès.');
            }
        } else {
            $manager = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
                'role'     => 'manager',
            ]);

            if (!empty($this->assignedEtablissementIds)) {
                Etablissement::whereIn('id', $this->assignedEtablissementIds)
                    ->update(['manager_id' => $manager->id]);
            }

            session()->flash('message', 'Manager créé avec succès.');
        }

        $this->resetForm();
        $this->loadData();
    }

    public function delete($id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $manager = User::find($id);

        if ($manager && $manager->role === 'manager') {
            // Detach establishments before deleting
            Etablissement::where('manager_id', $manager->id)->update(['manager_id' => null]);
            $manager->delete();
            session()->flash('message', 'Manager supprimé.');
            $this->loadData();
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->assignedEtablissementIds = [];
        $this->selectedManagerId = null;
        $this->isEditing = false;
        $this->isOpen = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.admin.dashboard.manager-manager');
        return $view->layout('layouts.dashboard');
    }
}
