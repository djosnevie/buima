<?php

namespace App\Livewire\Finance;

use App\Models\CategorieDepense;
use Livewire\Component;
use Livewire\WithPagination;

class CategorieDepenseManager extends Component
{
    use WithPagination;

    public $search = '';
    public $nom, $description, $categorieId;
    public $isModalOpen = false;

    protected $rules = [
        'nom' => 'required|min:3|max:255',
        'description' => 'nullable|max:500',
    ];

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['nom', 'description', 'categorieId']);

        if ($id) {
            $categorie = CategorieDepense::findOrFail($id);
            $this->categorieId = $id;
            $this->nom = $categorie->nom;
            $this->description = $categorie->description;
        }

        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        CategorieDepense::updateOrCreate(
            ['id' => $this->categorieId],
            [
                'etablissement_id' => auth()->user()->etablissement_id,
                'nom' => $this->nom,
                'description' => $this->description,
            ]
        );

        $this->isModalOpen = false;
        session()->flash('success', 'Catégorie enregistrée avec succès.');
    }

    public function delete($id)
    {
        CategorieDepense::find($id)?->delete();
        session()->flash('success', 'Catégorie supprimée.');
    }

    public function render()
    {
        $accessibleIds = auth()->user()->getAccessibleEtablissementIds();
        $categories = CategorieDepense::whereIn('etablissement_id', $accessibleIds)
            ->where('nom', 'like', '%' . $this->search . '%')
            ->paginate(10);

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.finance.categorie-depense-manager', [
            'categories' => $categories
        ]);
        return $view->layout('layouts.dashboard');
    }
}
