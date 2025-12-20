<?php

namespace App\Livewire\Orders;

use App\Models\Categorie;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Produit;
use App\Models\Table;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CreateOrder extends Component
{
    public $orderType = 'sur_place'; // sur_place, emporter, livraison
    public $selectedTable = null;
    public $cart = [];
    public $clientName = '';
    public $clientPhone = '';
    public $clientAddress = '';
    public $notes = '';
    public $selectedCategory = null;
    public $search = '';

    public function mount($table = null)
    {
        $this->selectedCategory = null; // Default to 'Tous'

        // Pre-fill table if coming from table list
        if ($table) {
            $this->selectedTable = (int) $table;
            $this->orderType = 'sur_place';
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function updatedSearch()
    {
        if ($this->search) {
            $this->selectedCategory = null;
        }
        // If search is cleared, we stay on 'Tous' (null) which is the default now
    }

    public function addToCart($produitId)
    {
        $produit = Produit::find($produitId);

        // Check if stock module is enabled
        if (auth()->user()->etablissement->hasModule('inventory')) {
            if (!$produit->hasSufficientStock(1)) {
                session()->flash('error', "Stock insuffisant pour {$produit->nom}.");
                return;
            }
        }

        if (isset($this->cart[$produitId])) {
            $this->cart[$produitId]['quantite']++;
        } else {
            $this->cart[$produitId] = [
                'produit_id' => $produit->id,
                'nom' => $produit->nom,
                'prix_unitaire' => $produit->prix_vente,
                'quantite' => 1,
            ];
        }
    }

    public function incrementQuantity($produitId)
    {
        if (isset($this->cart[$produitId])) {
            $produit = Produit::find($produitId);
            // Check module and stock
            if (auth()->user()->etablissement->hasModule('inventory')) {
                if ($produit && !$produit->hasSufficientStock($this->cart[$produitId]['quantite'] + 1)) {
                    session()->flash('error', "Stock insuffisant pour {$produit->nom}.");
                    return;
                }
            }
            $this->cart[$produitId]['quantite']++;
        }
    }

    public function decrementQuantity($produitId)
    {
        if (isset($this->cart[$produitId])) {
            if ($this->cart[$produitId]['quantite'] > 1) {
                $this->cart[$produitId]['quantite']--;
            } else {
                $this->removeFromCart($produitId);
            }
        }
    }

    public function removeFromCart($produitId)
    {
        unset($this->cart[$produitId]);
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['prix_unitaire'] * $item['quantite'];
        });
    }

    public function getTaxesProperty()
    {
        $taux = 0;
        if (auth()->user()->etablissement->tva_applicable) {
            $taux = auth()->user()->etablissement->tva_taux / 100;
        }
        return $this->subtotal * $taux;
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->taxes;
    }

    public function createOrder()
    {
        $this->validate([
            'orderType' => 'required|in:sur_place,emporter,livraison',
            'selectedTable' => 'required_if:orderType,sur_place',
            'clientName' => 'required_if:orderType,emporter,livraison|max:255',
            'clientPhone' => 'required_if:orderType,emporter,livraison|max:20',
            'cart' => 'required|array|min:1',
        ], [
            'selectedTable.required_if' => 'Veuillez sélectionner une table',
            'clientName.required_if' => 'Le nom du client est requis',
            'clientPhone.required_if' => 'Le téléphone du client est requis',
            'cart.required' => 'Le panier est vide',
            'cart.min' => 'Ajoutez au moins un produit',
        ]);

        DB::beginTransaction();

        try {
            // Create order
            $commande = Commande::create([
                'etablissement_id' => auth()->user()->etablissement_id,
                'table_id' => $this->orderType === 'sur_place' ? $this->selectedTable : null,
                'numero_commande' => Commande::generateOrderNumber(),
                'type_commande' => $this->orderType,
                'client_nom' => $this->clientName,
                'client_telephone' => $this->clientPhone,
                'client_adresse' => $this->clientAddress,
                'user_id' => auth()->id(),
                'caisse_id' => auth()->user()->caisse_id,
                'statut' => 'en_attente',
                'sous_total' => $this->subtotal,
                'montant_taxes' => $this->taxes,
                'total' => $this->total,
                'date_commande' => now(),
                'heure_prise' => now(),
                'notes' => $this->notes,
            ]);

            // Final stock check and deduction
            foreach ($this->cart as $item) {
                $produit = Produit::find($item['produit_id']);

                if ($produit && $produit->gestion_stock) {
                    // Start of Stock Module Check
                    if (auth()->user()->etablissement->hasModule('inventory')) {
                        if (!$produit->hasSufficientStock($item['quantite'])) {
                            throw new \Exception("Le produit {$produit->nom} n'a plus assez de stock.");
                        }

                        // Deduct stock
                        $stock = $produit->stock;
                        if ($stock) {
                            $qty = (int) $item['quantite'];
                            $old = (int) $stock->quantite;
                            $new = $old - $qty;
                            $stock->update(['quantite' => $new]);

                            \App\Models\MouvementStock::create([
                                'etablissement_id' => auth()->user()->etablissement_id,
                                'user_id' => auth()->id(),
                                'stockable_type' => Produit::class,
                                'stockable_id' => $produit->id,
                                'type' => 'sortie',
                                'quantite' => $qty,
                                'quantite_avant' => $old,
                                'quantite_apres' => $new,
                                'commentaire' => 'Commande #' . $commande->numero_commande,
                                'date_mouvement' => now(),
                            ]);
                        }
                    }
                    // If module is disabled, we do NOT deduc stock or check validity
                }
            }

            // Create order items
            foreach ($this->cart as $item) {
                CommandeItem::create([
                    'commande_id' => $commande->id,
                    'produit_id' => $item['produit_id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire'],
                    'statut' => 'en_attente',
                ]);
            }

            // Mark table as occupied if sur_place
            if ($this->orderType === 'sur_place' && $this->selectedTable) {
                Table::find($this->selectedTable)->markAsOccupied();
            }

            DB::commit();

            session()->flash('success', 'Commande créée avec succès !');

            // Reset form
            $this->reset(['cart', 'clientName', 'clientPhone', 'clientAddress', 'notes', 'selectedTable', 'orderType']);
            $this->mount(); // Re-initialize default values like selectedCategory

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la création de la commande : ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Optimiser toutes les requêtes
        $categories = Categorie::where('etablissement_id', auth()->user()->etablissement_id)
            ->active()
            ->select('id', 'nom')
            ->get();

        $produitsQuery = Produit::where('etablissement_id', auth()->user()->etablissement_id)
            ->available();

        if ($this->selectedCategory) {
            $produitsQuery->where('categorie_id', $this->selectedCategory);
        }

        if ($this->search) {
            $produitsQuery->where('nom', 'like', '%' . $this->search . '%');
        }

        $produits = $produitsQuery->get();

        $tables = Table::where('etablissement_id', auth()->user()->etablissement_id)
            ->select('id', 'numero', 'zone', 'capacite')
            ->available()
            ->orderBy('numero')
            ->get();

        return view('livewire.pages.orders.create', [
            'categories' => $categories,
            'produits' => $produits,
            'tables' => $tables,
        ]);
    }

}
