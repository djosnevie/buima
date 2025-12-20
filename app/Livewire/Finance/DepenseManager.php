<?php

namespace App\Livewire\Finance;

use App\Models\Depense;
use App\Models\CategorieDepense;
use Livewire\Component;
use Livewire\WithPagination;

class DepenseManager extends Component
{
    use WithPagination;

    public $search = '';
    public $montant, $description, $date_depense, $categorie_depense_id, $reference_piece, $depenseId;
    public $isModalOpen = false;

    protected $rules = [
        'montant' => 'required|numeric|min:0',
        'description' => 'required|min:3|max:500',
        'date_depense' => 'required|date',
        'categorie_depense_id' => 'required|exists:categorie_depenses,id',
        'reference_piece' => 'nullable|max:100',
    ];

    public function mount()
    {
        $this->date_depense = now()->format('Y-m-d');
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['montant', 'description', 'categorie_depense_id', 'reference_piece', 'depenseId']);
        $this->date_depense = now()->format('Y-m-d');

        if ($id) {
            $depense = Depense::findOrFail($id);
            $this->depenseId = $id;
            $this->montant = $depense->montant;
            $this->description = $depense->description;
            $this->date_depense = $depense->date_depense->format('Y-m-d');
            $this->categorie_depense_id = $depense->categorie_depense_id;
            $this->reference_piece = $depense->reference_piece;
        }

        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        Depense::updateOrCreate(
            ['id' => $this->depenseId],
            [
                'etablissement_id' => auth()->user()->etablissement_id,
                'user_id' => auth()->id(),
                'categorie_depense_id' => $this->categorie_depense_id,
                'montant' => $this->montant,
                'description' => $this->description,
                'date_depense' => $this->date_depense,
                'reference_piece' => $this->reference_piece,
            ]
        );

        $this->isModalOpen = false;
        session()->flash('success', 'Dépense enregistrée avec succès.');
    }

    public function delete($id)
    {
        Depense::find($id)?->delete();
        session()->flash('success', 'Dépense supprimée.');
    }

    public function render()
    {
        $accessibleIds = auth()->user()->getAccessibleEtablissementIds();
        $categories = CategorieDepense::whereIn('etablissement_id', $accessibleIds)->get();

        $depenses = Depense::with('categorieDepense')
            ->whereIn('etablissement_id', $accessibleIds)
            ->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                    ->orWhere('reference_piece', 'like', '%' . $this->search . '%');
            })
            ->orderBy('date_depense', 'desc')
            ->paginate(15);

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.finance.depense-manager', [
            'depenses' => $depenses,
            'categories' => $categories
        ]);
        return $view->layout('layouts.dashboard');
    }
}
