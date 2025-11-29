<?php

namespace App\Livewire\Products;

use App\Models\Categorie;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CategoryManager extends Component
{
    public $categories;
    public $nom;
    public $type = 'plat';
    public $couleur = '#3B82F6';
    public $editingId = null;

    protected $rules = [
        'nom' => 'required|min:2|max:50',
        'type' => 'required|in:entree,plat,dessert,boisson',
        'couleur' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Categorie::orderBy('ordre')->get();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nom' => $this->nom,
            'type' => $this->type,
            'couleur' => $this->couleur,
            'etablissement_id' => 1, // Default
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
        try {
            Categorie::find($id)->delete();
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
