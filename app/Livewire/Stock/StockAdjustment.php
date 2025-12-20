<?php

namespace App\Livewire\Stock;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\Ingredient;
use App\Models\StockProduit;
use Livewire\Component;

class StockAdjustment extends Component
{
    public $isOpen = false;
    public $typeItem; // 'produit' or 'ingredient'
    public $itemId;
    public $item;
    public $typeMouvement = 'entree'; // 'entree' or 'sortie'
    public $quantite;
    public $motif;
    public $seuilAlerte;

    protected $listeners = ['openAdjustmentModal' => 'open'];

    public function open($type, $id)
    {
        $this->typeItem = $type;
        $this->itemId = $id;
        $this->item = ($type === 'produit') ? Produit::with('stock')->find($id) : Ingredient::find($id);
        if ($this->item) {
            if ($type === 'produit') {
                $this->seuilAlerte = $this->item->stock->seuil_alerte ?? 0;
            } else {
                $this->seuilAlerte = $this->item->seuil_alerte ?? 0;
            }
        }
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->reset(['quantite', 'motif', 'typeMouvement']);
    }

    protected $rules = [
        'quantite' => 'nullable|numeric|min:0',
        'typeMouvement' => 'required|in:entree,sortie',
        'motif' => 'required_with:quantite|string|max:255',
        'seuilAlerte' => 'required|numeric|min:0',
    ];

    public function save()
    {
        $this->validate();

        $etablissement_id = auth()->user()->etablissement_id;
        $user_id = auth()->id();

        if ($this->typeItem === 'produit') {
            $stock = StockProduit::firstOrCreate(['produit_id' => $this->itemId]);
            $ancienStock = $stock->quantite ?? 0;

            $stock->seuil_alerte = $this->seuilAlerte;
            $stock->save();

            if ($this->quantite > 0) {
                if ($this->typeMouvement === 'entree') {
                    $stock->increment('quantite', $this->quantite);
                } else {
                    $stock->decrement('quantite', $this->quantite);
                }

                $nouveauStock = $stock->quantite ?? 0;

                MouvementStock::create([
                    'etablissement_id' => $etablissement_id,
                    'stockable_type' => Produit::class,
                    'stockable_id' => $this->itemId,
                    'type' => $this->typeMouvement,
                    'quantite' => $this->quantite,
                    'quantite_avant' => $ancienStock,
                    'quantite_apres' => $nouveauStock,
                    'motif' => $this->motif,
                    'user_id' => $user_id,
                    'date_mouvement' => now(),
                ]);
            }
        } else {
            $ingredient = Ingredient::find($this->itemId);
            $ancienStock = $ingredient->stock_actuel ?? 0;

            $ingredient->seuil_alerte = $this->seuilAlerte;

            if ($this->quantite > 0) {
                if ($this->typeMouvement === 'entree') {
                    $ingredient->increment('stock_actuel', $this->quantite);
                } else {
                    $ingredient->decrement('stock_actuel', $this->quantite);
                }
            }
            $ingredient->save();

            $nouveauStock = $ingredient->stock_actuel ?? 0;

            if ($this->quantite > 0) {
                MouvementStock::create([
                    'etablissement_id' => $etablissement_id,
                    'stockable_type' => Ingredient::class,
                    'stockable_id' => $this->itemId,
                    'type' => $this->typeMouvement,
                    'quantite' => $this->quantite,
                    'quantite_avant' => $ancienStock,
                    'quantite_apres' => $nouveauStock,
                    'motif' => $this->motif,
                    'user_id' => $user_id,
                    'date_mouvement' => now(),
                ]);
            }
        }

        $this->dispatch('stockUpdated');
        $this->close();
        session()->flash('success', 'Stock mis à jour avec succès.');
    }

    public function render()
    {
        return view('livewire.pages.stock.stock-adjustment');
    }
}
