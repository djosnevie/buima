<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\Caisse;
use App\Models\Section;
use App\Models\Etablissement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserManager extends Component
{
    public $users;
    public $sections; // For dropdown
    public $etablissements; // For Super Admin dropdown
    public $caisses; // For dropdown

    // Form properties
    public $name;
    public $email;
    public $password;
    public $role = 'user';
    public $section_id = '';
    public $caisse_id = ''; // Added
    public $etablissement_id = ''; // For Super Admin

    public $selectedUserId;
    public $isEditing = false;
    public $isOpen = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'role' => 'required|in:manager,admin,user',
        'section_id' => 'nullable|exists:sections,id',
        'caisse_id' => 'nullable|exists:caisses,id', // Added
        'etablissement_id' => 'nullable|exists:etablissements,id',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public $search = '';

    public function loadData()
    {
        if (Auth::user()->isSuperAdmin()) {
            $query = User::with(['etablissement', 'section', 'caisse']) // Modified
                ->where('id', '!=', Auth::id())
                ->latest();

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            }

            $this->users = $query->get();

            $this->sections = Section::with('etablissement')->where('actif', true)->get();
            $this->etablissements = Etablissement::all();
            $this->caisses = Caisse::all(); // Added
        } else {
            $query = Auth::user()->etablissement->users()
                ->where('id', '!=', Auth::id())
                ->with(['section', 'caisse']) // Modified
                ->latest();

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            }

            $this->users = $query->get();

            $this->sections = Auth::user()->etablissement->sections()->where('actif', true)->get();
            $this->caisses = Auth::user()->etablissement->caisses()->where('active', true)->get(); // Added
            $this->etablissements = collect();
        }
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
        $user = User::find($id);

        $hasAccess = Auth::user()->isSuperAdmin() ||
            ($user && $user->etablissement_id === Auth::user()->etablissement_id);

        if ($user && $hasAccess) {
            $this->selectedUserId = $id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->section_id = $user->section_id;
            $this->caisse_id = $user->caisse_id;
            $this->etablissement_id = $user->etablissement_id;
            $this->isEditing = true;
            $this->isOpen = true;
        }
    }

    public function save()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validationRules = $this->rules;
        if ($this->isEditing) {
            $validationRules['email'] = 'required|email|max:255|unique:users,email,' . $this->selectedUserId;
        } else {
            $validationRules['password'] = 'required|min:8';
        }

        $this->validate($validationRules);

        // Determine Etablissement ID
        $targetEtablissementId = Auth::user()->isSuperAdmin()
            ? ($this->etablissement_id ?: null)
            : Auth::user()->etablissement_id;

        // Restriction: Manager/Admin cannot create Super Admins
        // And Manager shouldn't create other Managers normally via this UI (they have POS Manager)
        $finalRole = $this->role;
        if (!Auth::user()->isSuperAdmin() && $finalRole === 'manager') {
            $finalRole = 'admin'; // Override if a non-superadmin tries to create a manager
        }

        if ($this->isEditing) {
            $user = User::find($this->selectedUserId);
            $hasAccess = Auth::user()->isSuperAdmin() ||
                ($user && $user->etablissement_id === Auth::user()->etablissement_id);

            if ($user && $hasAccess) {
                $data = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'role' => $finalRole,
                    'section_id' => $this->section_id ?: null,
                    'caisse_id' => $this->caisse_id ?: null,
                    'etablissement_id' => $targetEtablissementId,
                ];
                if ($this->password) {
                    $data['password'] = Hash::make($this->password);
                }
                $user->update($data);
                session()->flash('message', 'Utilisateur mis à jour.');
            }
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $finalRole,
                'section_id' => $this->section_id ?: null,
                'caisse_id' => $this->caisse_id ?: null,
                'etablissement_id' => $targetEtablissementId,
            ]);
            session()->flash('message', 'Utilisateur créé avec succès pour l\'établissement actif.');
        }

        $this->resetForm();
        $this->loadData();
    }

    public function delete($id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $user = User::find($id);
        $hasAccess = Auth::user()->isSuperAdmin() ||
            ($user && $user->etablissement_id === Auth::user()->etablissement_id);

        if ($user && $hasAccess) {
            $user->delete();
            session()->flash('message', 'Utilisateur supprimé.');
            $this->loadData();
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'user';
        $this->section_id = '';
        $this->caisse_id = '';
        $this->etablissement_id = '';
        $this->selectedUserId = null;
        $this->isEditing = false;
        $this->isOpen = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.admin.users.user-manager');
        return $view->layout('layouts.dashboard');
    }
}