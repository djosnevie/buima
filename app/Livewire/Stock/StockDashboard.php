<?php

namespace App\Livewire\Stock;

use App\Models\Produit;
use App\Models\Ingredient;
use App\Models\MouvementStock;
use Livewire\Component;
use Livewire\WithPagination;

class StockDashboard extends Component
{
    use WithPagination;

    public $activeTab = 'produits';
    public $search = '';
    public $showHistoryModal = false;
    public $historyItem = null;
    public $itemMovements = [];

    // Ingredient Form
    public $showIngredientModal = false;
    public $ingredientId = null;
    public $nom, $unite, $seuil_alerte, $stock_actuel = 0;

    protected $listeners = ['stockUpdated' => '$refresh'];

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function showHistory($type, $id)
    {
        $this->historyItem = ($type === 'produit') ? Produit::find($id) : Ingredient::find($id);
        $this->itemMovements = MouvementStock::where('stockable_type', ($type === 'produit') ? Produit::class : Ingredient::class)
            ->where('stockable_id', $id)
            ->latest()
            ->get();
        $this->showHistoryModal = true;
    }

    public function openIngredientModal($id = null)
    {
        $this->reset('ingredientId', 'nom', 'unite', 'seuil_alerte', 'stock_actuel');
        if ($id) {
            $ingredient = Ingredient::findOrFail($id);
            $this->ingredientId = $id;
            $this->nom = $ingredient->nom;
            $this->unite = $ingredient->unite;
            $this->seuil_alerte = $ingredient->seuil_alerte;
            $this->stock_actuel = $ingredient->stock_actuel;
        }
        $this->showIngredientModal = true;
    }

    public function saveIngredient()
    {
        $this->validate([
            'nom' => 'required|string|max:255',
            'unite' => 'nullable|string|max:50',
            'seuil_alerte' => 'nullable|numeric|min:0',
            'stock_actuel' => 'nullable|numeric|min:0',
        ]);

        $data = [
            'etablissement_id' => auth()->user()->etablissement_id,
            'nom' => $this->nom,
            'unite' => $this->unite,
            'seuil_alerte' => $this->seuil_alerte ?? 0,
            'stock_actuel' => $this->stock_actuel ?? 0,
        ];

        if ($this->ingredientId) {
            Ingredient::find($this->ingredientId)->update($data);
        } else {
            Ingredient::create($data);
        }

        $this->showIngredientModal = false;
        session()->flash('success', 'Ingrédient enregistré avec succès.');
    }

    public function exportStock()
    {
        $accessibleIds = auth()->user()->getAccessibleEtablissementIds();
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=stock_export_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Type', 'Nom', 'Stock Actuel', 'Seuil Alerte', 'Unite/Categorie'];

        $callback = function () use ($accessibleIds, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Produit::whereIn('etablissement_id', $accessibleIds)
                ->where('gestion_stock', true)
                ->with('stock')
                ->chunk(100, function ($produits) use ($file) {
                    foreach ($produits as $p) {
                        fputcsv($file, ['Produit', $p->nom, $p->stock->quantite ?? 0, $p->stock->seuil_alerte ?? 0, $p->categorie->nom ?? '']);
                    }
                });

            Ingredient::whereIn('etablissement_id', $accessibleIds)
                ->chunk(100, function ($ingredients) use ($file) {
                    foreach ($ingredients as $i) {
                        fputcsv($file, ['Ingredient', $i->nom, $i->stock_actuel, $i->seuil_alerte, $i->unite]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        if (auth()->user()->isManager() && session('manager_view_site_id')) {
            $accessibleIds = [session('manager_view_site_id')];
        } else {
            $accessibleIds = auth()->user()->getAccessibleEtablissementIds();
        }

        if ($this->activeTab === 'produits') {
            $items = Produit::whereIn('etablissement_id', $accessibleIds)
                ->where('gestion_stock', true)
                ->where('nom', 'like', '%' . $this->search . '%')
                ->with(['stock'])
                ->paginate(10);
        } else {
            $items = Ingredient::whereIn('etablissement_id', $accessibleIds)
                ->where('nom', 'like', '%' . $this->search . '%')
                ->paginate(10);
        }

        // Base query for counts
        $stockOutCount = Produit::whereIn('etablissement_id', $accessibleIds)
            ->where('gestion_stock', true)
            ->whereHas('stock', function ($q) {
                $q->where('quantite', '<=', 0);
            })->count();

        $stockLowCount = Produit::whereIn('etablissement_id', $accessibleIds)
            ->where('gestion_stock', true)
            ->whereHas('stock', function ($q) {
                $q->where('quantite', '>', 0)
                    ->whereColumn('quantite', '<=', 'seuil_alerte');
            })->count();

        // Ingredients
        $stockOutCount += Ingredient::whereIn('etablissement_id', $accessibleIds)
            ->where('stock_actuel', '<=', 0) // Less than or equal to 0 is out
            ->count();

        $stockLowCount += Ingredient::whereIn('etablissement_id', $accessibleIds)
            ->where('stock_actuel', '>', 0)
            ->whereColumn('stock_actuel', '<=', 'seuil_alerte')
            ->count();

        $recentMovements = MouvementStock::whereIn('etablissement_id', $accessibleIds)
            ->with(['stockable', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.pages.stock.stock-dashboard', [
            'items' => $items,
            'stockOutCount' => $stockOutCount,
            'stockLowCount' => $stockLowCount,
            'recentMovements' => $recentMovements
        ])->layout('layouts.dashboard');
    }
}
