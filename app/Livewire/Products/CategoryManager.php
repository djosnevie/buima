<?php

namespace App\Livewire\Products;

use App\Models\Categorie;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class CategoryManager extends Component
{
    use WithFileUploads;

    public $categories;
    public $nom;
    public $type = 'plat';
    public $couleur = '#3B82F6';
    public $editingId = null;
    public $importFile;

    protected $rules = [
        'nom' => 'required|min:2|max:50',
        'type' => 'required|in:entree,plat,dessert,boisson',
        'couleur' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function exportExcel()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CategoriesExport, 'categories_omenu.xlsx');
    }

    public function updatedImportFile()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $this->validate([
            'importFile' => 'required|mimes:xlsx,csv,xls|max:10240',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\CategoriesImport, $this->importFile);
            session()->flash('message', 'Catégories importées avec succès.');
            $this->loadCategories();
            $this->dispatch('category-updated');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'importation. Vérifiez le format du fichier.');
        }

        $this->reset('importFile');
    }

    public function loadCategories()
    {
        $this->categories = Categorie::where('etablissement_id', auth()->user()->etablissement_id)
            ->orderBy('ordre')
            ->get();
    }

    public function save()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $this->validate();

        $data = [
            'nom' => $this->nom,
            'type' => $this->type,
            'couleur' => $this->couleur,
            'etablissement_id' => auth()->user()->etablissement_id,
        ];

        if ($this->editingId) {
            $category = Categorie::find($this->editingId);
            $category->update($data);
            session()->flash('message', 'Catégorie mise à jour.');
        } else {
            // Auto-assign order
            $data['ordre'] = Categorie::max('ordre') + 1;
            Categorie::create($data);
            session()->flash('message', 'Catégorie créée.');
        }

        $this->resetForm();
        $this->loadCategories();
        $this->dispatch('category-updated'); // Notify parent if needed
    }

    public function edit($id)
    {
        $category = Categorie::find($id);
        $this->editingId = $category->id;
        $this->nom = $category->nom;
        $this->type = $category->type;
        $this->couleur = $category->couleur;
    }

    public function delete($id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        try {
            $category = Categorie::find($id);
            if (!$category) return;

            // Block deletion if any product in this category is in an active order
            $hasActiveOrders = \App\Models\CommandeItem::whereHas('produit', function ($q) use ($id) {
                    $q->where('categorie_id', $id);
                })
                ->whereHas('commande', function ($q) {
                    $q->whereIn('statut', ['en_attente', 'servie']);
                })
                ->exists();

            if ($hasActiveOrders) {
                session()->flash('error', 'Impossible : Des produits de cette catégorie sont utilisés dans des commandes en cours.');
                return;
            }

            $category->delete(); // Soft delete
            session()->flash('message', 'Catégorie supprimée.');
            $this->loadCategories();
            $this->dispatch('category-updated');
        } catch (\Exception $e) {
            session()->flash('error', 'Impossible de supprimer cette catégorie (peut-être utilisée).');
        }
    }

    public function resetForm()
    {
        $this->nom = '';
        $this->type = 'plat';
        $this->couleur = '#3B82F6';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.pages.products.category-manager');
    }
}
