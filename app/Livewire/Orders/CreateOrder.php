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

    public $existingOrderId = null;
    public $showManagerPinModal = false;
    public $managerPassword = '';
    public $pendingRemovalProduitId = null;
    public $pendingReductionProduitId = null;

    public function mount($table = null, $orderId = null)
    {
        // Sales are exclusively for caissiers
        if (auth()->user()->isAdmin()) {
            session()->flash('error', 'Les ventes sont réservées aux caissiers. Un manager ou administrateur ne peut pas créer de commandes.');
            $this->redirect(route('orders.index'));
            return;
        }

        $this->selectedCategory = null; // Default to 'Tous'

        // Pre-fill table if coming from table list
        if ($table) {
            $this->selectedTable = (int) $table;
            $this->orderType = 'sur_place';
        }

        if ($orderId) {
            $this->existingOrderId = $orderId;
            $commande = Commande::with('items.produit')->find($orderId);
            if ($commande) {
                if (in_array($commande->statut, ['payee', 'annulee'])) {
                     session()->flash('error', 'Cette commande est verrouillée (payée ou annulée) et ne peut plus être modifiée.');
                     $this->redirect(route('orders.index'));
                     return;
                }

                $this->orderType = $commande->type_commande;
                $this->clientName = $commande->client_nom ?? '';
                $this->clientPhone = $commande->client_telephone ?? '';
                $this->clientAddress = $commande->client_adresse ?? '';
                $this->notes = $commande->notes ?? '';
                $this->selectedTable = $commande->table_id;
                
                foreach ($commande->items as $item) {
                    $this->cart[$item->produit_id] = [
                        'produit_id' => $item->produit_id,
                        'nom' => $item->produit ? $item->produit->nom : 'Produit inconnu',
                        'prix_unitaire' => $item->prix_unitaire,
                        'quantite' => $item->quantite,
                    ];
                }
            }
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
            if (!auth()->user()->isManager() && !auth()->user()->isAdmin()) {
                $this->pendingReductionProduitId = $produitId;
                $this->pendingRemovalProduitId = null;
                $this->showManagerPinModal = true;
                return;
            }

            if ($this->cart[$produitId]['quantite'] > 1) {
                $this->cart[$produitId]['quantite']--;
            } else {
                unset($this->cart[$produitId]);
            }
        }
    }

    public function removeFromCart($produitId)
    {
        if (!auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            $this->pendingRemovalProduitId = $produitId;
            $this->pendingReductionProduitId = null;
            $this->showManagerPinModal = true;
            return;
        }

        unset($this->cart[$produitId]);
    }

    public function validateManagerApproval()
    {
        $this->resetErrorBag('managerPassword');

        if (empty($this->managerPassword)) {
            $this->addError('managerPassword', 'Veuillez saisir un mot de passe.');
            return;
        }

        $managers = \App\Models\User::where('etablissement_id', auth()->user()->etablissement_id)
            ->whereIn('role', ['manager', 'admin'])
            ->get();
            
        $isValid = false;
        foreach ($managers as $mgr) {
            if (\Illuminate\Support\Facades\Hash::check($this->managerPassword, $mgr->password)) {
                $isValid = true;
                break;
            }
        }

        // Also check super admins just in case
        if (!$isValid && auth()->user()->etablissement->manager_id) {
             $owner = \App\Models\User::find(auth()->user()->etablissement->manager_id);
             if ($owner && \Illuminate\Support\Facades\Hash::check($this->managerPassword, $owner->password)) {
                 $isValid = true;
             }
        }

        if ($isValid) {
            if ($this->pendingReductionProduitId) {
                if (isset($this->cart[$this->pendingReductionProduitId])) {
                    if ($this->cart[$this->pendingReductionProduitId]['quantite'] > 1) {
                        $this->cart[$this->pendingReductionProduitId]['quantite']--;
                    } else {
                        unset($this->cart[$this->pendingReductionProduitId]);
                    }
                }
                $this->pendingReductionProduitId = null;
            } elseif ($this->pendingRemovalProduitId) {
                unset($this->cart[$this->pendingRemovalProduitId]);
                $this->pendingRemovalProduitId = null;
            }
            $this->showManagerPinModal = false;
            $this->managerPassword = '';
        } else {
            $this->addError('managerPassword', 'Mot de passe incorrect ou non autorisé.');
        }
    }

    public function cancelManagerApproval()
    {
        $this->showManagerPinModal = false;
        $this->managerPassword = '';
        $this->pendingRemovalProduitId = null;
        $this->pendingReductionProduitId = null;
        $this->resetErrorBag('managerPassword');
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
            $isEditing = $this->existingOrderId !== null;
            $commande = null;

            if ($isEditing) {
                $commande = Commande::find($this->existingOrderId);
                if (!$commande) {
                    throw new \Exception("Commande introuvable.");
                }

                // Revert previous stock if module is active
                if (auth()->user()->etablissement->hasModule('inventory')) {
                    foreach ($commande->items as $oldItem) {
                        $produit = Produit::find($oldItem->produit_id);
                        if ($produit && $produit->gestion_stock && $produit->stock) {
                            $stock = $produit->stock;
                            $qty = (int) $oldItem->quantite;
                            $old = (int) $stock->quantite;
                            $new = $old + $qty;
                            $stock->update(['quantite' => $new]);

                            // Optionally log reverse movement or just let the new deduction represent the change
                            \App\Models\MouvementStock::create([
                                'etablissement_id' => auth()->user()->etablissement_id,
                                'user_id' => auth()->id(),
                                'stockable_type' => Produit::class,
                                'stockable_id' => $produit->id,
                                'type' => 'entree',
                                'quantite' => $qty,
                                'quantite_avant' => $old,
                                'quantite_apres' => $new,
                                'commentaire' => 'Modification Commande #' . $commande->numero_commande . ' (Annulation stock précédent)',
                                'date_mouvement' => now(),
                            ]);
                        }
                    }
                }

                // Delete old items
                $commande->items()->delete();

                // Update order amounts and info
                $commande->update([
                    'table_id' => $this->orderType === 'sur_place' ? $this->selectedTable : null,
                    'type_commande' => $this->orderType,
                    'client_nom' => $this->clientName,
                    'client_telephone' => $this->clientPhone,
                    'client_adresse' => $this->clientAddress,
                    'sous_total' => $this->subtotal,
                    'montant_taxes' => $this->taxes,
                    'total' => $this->total,
                    'notes' => $this->notes,
                ]);

            } else {
                // Get active session for caissiers
                $activeSession = auth()->user()->activeSession();

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
                    'caisse_id' => $activeSession?->caisse_id ?? auth()->user()->caisse_id,
                    'session_caisse_id' => $activeSession?->id,
                    'statut' => 'en_attente',
                    'sous_total' => $this->subtotal,
                    'montant_taxes' => $this->taxes,
                    'total' => $this->total,
                    'date_commande' => now(),
                    'heure_prise' => now(),
                    'notes' => $this->notes,
                ]);
            }

            // Final stock check and deduction for new cart
            foreach ($this->cart as $item) {
                $produit = Produit::find($item['produit_id']);

                if ($produit && $produit->gestion_stock) {
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
                                'commentaire' => ($isEditing ? 'Modification ' : '') . 'Commande #' . $commande->numero_commande,
                                'date_mouvement' => now(),
                            ]);
                        }
                    }
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

            session()->flash('success', $isEditing ? 'Commande modifiée avec succès !' : 'Commande créée avec succès !');

            if ($isEditing) {
                return redirect()->route('orders.index');
            }

            // Reset form
            $this->reset(['cart', 'clientName', 'clientPhone', 'clientAddress', 'notes', 'selectedTable', 'orderType', 'existingOrderId']);
            $this->mount(); // Re-initialize default values

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur : ' . $e->getMessage());
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
