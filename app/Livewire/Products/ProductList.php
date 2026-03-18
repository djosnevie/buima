<?php

namespace App\Livewire\Products;

use App\Models\Produit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProductList extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $typeFilter = 'tous';
    public $showCategoryManager = false;
    public $importFile;

    public function setFilter($filter)
    {
        $this->typeFilter = $filter;
        $this->resetPage();
    }

    public function toggleCategoryManager()
    {
        $this->showCategoryManager = !$this->showCategoryManager;
    }

    public function exportExcel()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ProductsExport, 'produits_omenu.xlsx');
    }

    public function updatedImportFile()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $this->validate([
            'importFile' => 'required|mimes:xlsx,csv,xls|max:10240', // 10MB max
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ProductsImport, $this->importFile);
            session()->flash('success', 'Produits importés avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'importation : Veuillez vérifier le format de votre fichier.');
        }

        $this->reset('importFile');
    }

    public function delete($id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $produit = Produit::find($id);
        if ($produit) {
            // Un produit ne peut pas être supprimé s'il est dans une commande "en attente" ou "servie"
            $hasActiveOrders = \App\Models\CommandeItem::where('produit_id', $produit->id)
                ->whereHas('commande', function ($q) {
                    $q->whereIn('statut', ['en_attente', 'servie']);
                })
                ->exists();

            if ($hasActiveOrders) {
                session()->flash('error', 'Impossible : Ce produit est utilisé dans une ou plusieurs commandes en cours (en attente ou servie).');
                return;
            }

            try {
                // Soft deletes preserve data for historic 'payee' and 'annulee' commands
                $produit->delete();
                session()->flash('success', 'Produit supprimé avec succès.');
            } catch (\Exception $e) {
                session()->flash('error', 'Une erreur est survenue lors de la suppression.');
            }
        }
    }

    public function render()
    {
        $query = Produit::with('categorie');

        if (auth()->user()->isManager()) {
            $contextSiteId = session('manager_view_site_id');
            if ($contextSiteId) {
                $query->where('etablissement_id', $contextSiteId);
            } else {
                $query->whereIn('etablissement_id', auth()->user()->getAccessibleEtablissementIds());
            }
        } else {
            $query->where('etablissement_id', auth()->user()->etablissement_id);
        }

        $query->latest();

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
