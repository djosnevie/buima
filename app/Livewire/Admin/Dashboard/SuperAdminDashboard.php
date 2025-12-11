<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SuperAdminDashboard extends Component
{
    public $etablissements;
    public $totalUsers;
    public $totalRestaurants;

    public function mount()
    {
        if (!Auth::user()->isSuperAdmin()) {
            return redirect()->route('dashboard');
        }

        $this->loadData();
    }

    public $recentRestaurants;
    public $newRestaurantsCount;
    public $topRestaurant;

    public $search = '';

    public function loadData()
    {
        $query = Etablissement::with('users')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $this->etablissements = $query->get();
        $this->totalRestaurants = Etablissement::count(); // Keep total unaffected by search
        $this->totalUsers = User::count();

        // New Metrics: Platform Growth & Performance
        $this->newRestaurantsCount = Etablissement::where('created_at', '>=', now()->subDays(30))->count();

        // Find top restaurant by order volume
        $this->topRestaurant = Etablissement::withCount('commandes')
            ->orderByDesc('commandes_count')
            ->first();

        $this->recentRestaurants = Etablissement::latest()->take(5)->get();
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public $selectedId;
    public $nom;
    public $adresse;
    public $telephone;
    public $email;
    public $type;
    public $devise;
    public $theme_color = '#ff6b35'; // Default primary
    public $secondary_color = '#ff9f43'; // Default secondary
    public $button_color = '#ff6b35'; // Default button
    public $isOpen = false;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'type' => 'required|in:avec_tables,sans_tables,mixte',
        'adresse' => 'nullable|string|max:500',
        'telephone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'devise' => 'required|string|max:5',
        'theme_color' => 'nullable|string|max:7',
        'secondary_color' => 'nullable|string|max:7',
        'button_color' => 'nullable|string|max:7',
    ];

    public function edit($id)
    {
        $etablissement = Etablissement::find($id);
        if ($etablissement) {
            $this->selectedId = $id;
            $this->nom = $etablissement->nom;
            $this->adresse = $etablissement->adresse;
            $this->telephone = $etablissement->telephone;
            $this->email = $etablissement->email;
            $this->type = $etablissement->type;
            $this->devise = $etablissement->devise;
            $this->theme_color = $etablissement->theme_color ?? '#ff6b35';
            $this->secondary_color = $etablissement->secondary_color ?? '#ff9f43';
            $this->button_color = $etablissement->button_color ?? '#ff6b35';
            $this->isOpen = true;
        }
    }

    public function update()
    {
        $this->validate();

        if ($this->selectedId) {
            $etablissement = Etablissement::find($this->selectedId);
            if ($etablissement) {
                $etablissement->update([
                    'nom' => $this->nom,
                    'adresse' => $this->adresse,
                    'telephone' => $this->telephone,
                    'email' => $this->email,
                    'type' => $this->type,
                    'devise' => $this->devise,
                    'theme_color' => $this->theme_color,
                    'secondary_color' => $this->secondary_color,
                    'button_color' => $this->button_color,
                ]);
                session()->flash('message', 'Restaurant mis à jour avec succès.');
                $this->resetForm();
                $this->loadData();
            }
        }
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nom = '';
        $this->adresse = '';
        $this->telephone = '';
        $this->email = '';
        $this->type = '';
        $this->devise = '';
        $this->theme_color = '#ff6b35';
        $this->secondary_color = '#ff9f43';
        $this->button_color = '#ff6b35';
        $this->isOpen = false;
        $this->resetErrorBag();
    }

    public function deleteRestaurant($id)
    {
        $etablissement = Etablissement::find($id);
        if ($etablissement) {
            $etablissement->delete();
            session()->flash('message', 'Restaurant supprimé.');
            $this->loadData();
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard.super-admin-dashboard')->layout('layouts.dashboard');
    }
}