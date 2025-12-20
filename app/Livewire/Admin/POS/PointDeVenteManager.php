<?php

namespace App\Livewire\Admin\POS;

use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class PointDeVenteManager extends Component
{
    public $etablissements;

    public function mount()
    {
        if (!Auth::user()->isManager()) {
            return redirect()->route('dashboard');
        }

        $this->loadData();
    }

    public $search = '';

    public function loadData()
    {
        $user = Auth::user();
        $query = Etablissement::where('manager_id', $user->id)->with('users')->latest();

        if ($this->search) {
            $query->where('nom', 'like', '%' . $this->search . '%');
        }

        $this->etablissements = $query->get();
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
    public $type = 'mixte';
    public $devise = 'XAF';
    public $isOpen = false;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'type' => 'required|in:avec_tables,sans_tables,mixte',
        'adresse' => 'nullable|string|max:500',
        'telephone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'devise' => 'required|string|max:5',
    ];

    public function openModal()
    {
        $this->resetForm();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $etablissement = Etablissement::where('id', $id)
            ->where('manager_id', Auth::id())
            ->first();

        if ($etablissement) {
            $this->selectedId = $id;
            $this->nom = $etablissement->nom;
            $this->adresse = $etablissement->adresse;
            $this->telephone = $etablissement->telephone;
            $this->email = $etablissement->email;
            $this->type = $etablissement->type;
            $this->devise = $etablissement->devise;
            $this->isOpen = true;
        }
    }

    public function save()
    {
        $this->validate();

        $manager = Auth::user();

        // Fetch primary establishment (the parent) for settings propagation
        $parent = Etablissement::where('manager_id', $manager->id)
            ->oldest()
            ->first();

        $data = [
            'nom' => $this->nom,
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'type' => $this->type,
            'devise' => $this->devise,
            'manager_id' => $manager->id,
            'slug' => Str::slug($this->nom . '-' . Str::random(5)),
            'actif' => true,
        ];

        // Inherit settings if creating new
        if (!$this->selectedId && $parent) {
            $data['modules'] = $parent->modules;
            $data['theme_color'] = $parent->theme_color;
            $data['secondary_color'] = $parent->secondary_color;
            $data['button_color'] = $parent->button_color;
            $data['devise'] = $parent->devise; // Override with parent devise
        } elseif (!$this->selectedId && !$parent) {
            // Fallback for very first resto (though usually created via RestaurantCreate)
            $data['modules'] = ['orders', 'products', 'tables', 'pos'];
        }

        if ($this->selectedId) {
            // When updating, we don't necessarily override modules unless explicitly handled
            Etablissement::where('id', $this->selectedId)
                ->where('manager_id', $manager->id)
                ->update(collect($data)->except(['slug', 'manager_id', 'modules'])->toArray());
            session()->flash('message', 'Point de vente mis à jour.');
        } else {
            Etablissement::create($data);
            session()->flash('message', 'Nouveau point de vente créé avec les configurations de votre établissement principal.');
        }

        $this->isOpen = false;
        $this->loadData();
    }

    public function switchEtablissement($id)
    {
        $etablissement = Etablissement::where('id', $id)
            ->where('manager_id', Auth::id())
            ->first();

        if ($etablissement) {
            $user = Auth::user();
            $user->etablissement_id = $id;
            $user->save();

            session()->flash('message', 'Établissement activé : ' . $etablissement->nom);
            return redirect()->route('dashboard');
        }
    }

    public function resetForm()
    {
        $this->selectedId = null;
        $this->nom = '';
        $this->adresse = '';
        $this->telephone = '';
        $this->email = '';
        $this->type = 'mixte';
        $this->devise = 'XAF';
        $this->resetErrorBag();
    }

    public function render()
    {
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.admin.pos.point-de-vente-manager');
        return $view->layout('layouts.dashboard');
    }
}
