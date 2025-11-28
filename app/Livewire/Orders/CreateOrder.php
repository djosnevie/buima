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

    public function mount()
    {
        $firstCategory = Categorie::active()->first();
        $this->selectedCategory = $firstCategory?->id;
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function addToCart($produitId)
    {
        $produit = Produit::find($produitId);

        if (!$produit)
            return;

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
        return $this->subtotal * 0.10;
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
                'etablissement_id' => auth()->user()->etablissement_id ?? 1,
                'table_id' => $this->orderType === 'sur_place' ? $this->selectedTable : null,
                'numero_commande' => Commande::generateOrderNumber(),
                'type_commande' => $this->orderType,
                'client_nom' => $this->clientName,
                'client_telephone' => $this->clientPhone,
                'client_adresse' => $this->clientAddress,
                'user_id' => auth()->id(),
                'statut' => 'en_attente',
                'sous_total' => $this->subtotal,
                'montant_taxes' => $this->taxes,
                'total' => $this->total,
                'date_commande' => now(),
                'heure_prise' => now(),
                'notes' => $this->notes,
            ]);

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

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la création de la commande : ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Optimiser toutes les requêtes
        $categories = Categorie::active()
            ->select('id', 'nom')
            ->get();

        $produits = $this->selectedCategory
            ? Produit::select('id', 'nom', 'prix_vente')
                ->where('categorie_id', $this->selectedCategory)
                ->available()
                ->get()
            : collect();

        $tables = Table::select('id', 'numero', 'zone', 'capacite')
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
