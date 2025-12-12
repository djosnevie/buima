<?php

namespace App\Livewire\Products;

use App\Models\Produit;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class ProductList extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = 'tous';
    public $showCategoryManager = false;

    public function setFilter($filter)
    {
        $this->typeFilter = $filter;
        $this->resetPage();
    }

    public function toggleCategoryManager()
    {
        $this->showCategoryManager = !$this->showCategoryManager;
    }

    public function delete($id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $produit = Produit::find($id);
        if ($produit) {
            try {
                if ($produit->image) {
                    Storage::disk('public')->delete($produit->image);
                }
                $produit->delete();
                session()->flash('success', 'Produit supprimé avec succès.');
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a foreign key constraint error
                if ($e->getCode() == '23000') {
                    session()->flash('error', 'Impossible de supprimer ce produit car il est utilisé dans des commandes.');
                } else {
                    session()->flash('error', 'Une erreur est survenue lors de la suppression.');
                }
            }
        }
    }

    public function render()
    {
        $query = Produit::with('categorie')
            ->where('etablissement_id', auth()->user()->etablissement_id)
            ->latest();

        if ($this->search) {
            $query->where('nom', 'like', '%' . $this->search . '%');
        }

        if ($this->typeFilter !== 'tous') {
            $query->whereHas('categorie', function ($q) {
                if ($this->typeFilter === 'plat') {
                    // Include all food types if filter is 'plat'
                    $q->whereIn('type', ['plat', 'entree', 'dessert']);
                } else {
                    $q->where('type', $this->typeFilter);
                }
            });
        }

        return view('livewire.pages.products.product-list', [
            'produits' => $query->paginate(10)
        ])->layout('layouts.dashboard');
    }
}
