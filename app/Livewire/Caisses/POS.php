<?php

namespace App\Livewire\Caisses;

use App\Models\Caisse;
use App\Models\Categorie;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Produit;
use App\Models\SessionCaisse;
use App\Models\Transaction;
use App\Models\MouvementStock;
use Livewire\Component;
use Livewire\WithPagination;

class POS extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = null;
    public $cart = [];
    public $activeSession = null;
    public $showSessionModal = false;
    public $caisseId;
    public $montant_ouverture;

    public $orderType = 'emporter'; // Default for POS
    public $paymentMethod = 'especes';

    public function mount()
    {
        // Sales are exclusively for caissiers
        if (auth()->user()->isAdmin()) {
            session()->flash('error', 'Les ventes sont réservées aux caissiers. Un manager ou administrateur ne peut pas vendre.');
            $this->redirect(route('dashboard'));
            return;
        }

        $this->checkActiveSession();
    }

    public function checkActiveSession()
    {
        // Check if current user has an open session in any caisse of this establishment
        $this->activeSession = SessionCaisse::where('user_id', auth()->id())
            ->where('statut', 'ouverte')
            ->whereHas('caisse', function ($q) {
                $q->where('etablissement_id', auth()->user()->etablissement_id);
            })
            ->first();

        if (!$this->activeSession) {
            $this->showSessionModal = true;
        }
    }

    public function openSession()
    {
        $this->validate([
            'caisseId' => 'required|exists:caisses,id',
            'montant_ouverture' => 'required|numeric|min:0',
        ]);

        $caisse = Caisse::find($this->caisseId);
        if ($caisse->currentSession()) {
            $this->addError('caisseId', 'Cette caisse a déjà une session ouverte.');
            return;
        }

        $this->activeSession = SessionCaisse::create([
            'caisse_id' => $this->caisseId,
            'user_id' => auth()->id(),
            'date_ouverture' => now(),
            'montant_ouverture' => $this->montant_ouverture,
            'statut' => 'ouverte',
        ]);

        $this->showSessionModal = false;
        session()->flash('success', 'Session de caisse ouverte.');
    }

    public function addToCart($produitId)
    {
        $produit = Produit::find($produitId);
        if (!$produit || !$produit->hasSufficientStock(1)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Stock insuffisant.']);
            return;
        }

        if (isset($this->cart[$produitId])) {
            $this->cart[$produitId]['quantite']++;
        } else {
            $this->cart[$produitId] = [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'prix' => $produit->prix_vente,
                'quantite' => max(1, $produit->quantite_minimum ?? 1),
            ];
        }
    }

    public function removeFromCart($produitId)
    {
        unset($this->cart[$produitId]);
    }

    public function updateQuantity($produitId, $delta)
    {
        if (isset($this->cart[$produitId])) {
            $newQty = $this->cart[$produitId]['quantite'] + $delta;
            $produit = Produit::find($produitId);
            $minQty = $produit->quantite_minimum ?? 1;

            if ($newQty >= $minQty) {
                if ($delta > 0 && !$produit->hasSufficientStock($newQty)) {
                    $this->dispatch('notify', ['type' => 'error', 'message' => 'Stock insuffisant.']);
                    return;
                }
                $this->cart[$produitId]['quantite'] = $newQty;
            } else {
                $this->removeFromCart($produitId);
            }
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['prix'] * $item['quantite']);
    }

    public function processOrder()
    {
        if (empty($this->cart)) return;

        // Reload session to get the freshest state
        $this->checkActiveSession();

        if (!$this->activeSession) {
            session()->flash('error', 'Veuillez ouvrir une caisse avant de commencer les opérations de vente.');
            $this->showSessionModal = true;
            return;
        }

        $commande = \DB::transaction(function () {
            // Create Commande
            $commande = Commande::create([
                'etablissement_id' => auth()->user()->etablissement_id,
                'user_id' => auth()->id(),
                'caisse_id' => $this->activeSession->caisse_id,
                'session_caisse_id' => $this->activeSession->id,
                'statut' => 'payee',
                'total' => $this->subtotal,
                'sous_total' => $this->subtotal,
                'numero_commande' => Commande::generateOrderNumber('POS'),
                'type_commande' => $this->orderType,
                'date_commande' => now(),
                'heure_prise' => now(),
            ]);

            // Create Items
            foreach ($this->cart as $item) {
                CommandeItem::create([
                    'commande_id' => $commande->id,
                    'produit_id' => $item['id'],
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix'],
                    'total' => $item['prix'] * $item['quantite'],
                ]);

                // Update Stock
                $produit = Produit::find($item['id']);
                if ($produit && $produit->gestion_stock) {
                    $stock = $produit->stock;
                    if ($stock) {
                        $quantiteVendue = (int) $item['quantite'];
                        $ancien = (int) $stock->quantite;
                        $nouveau = $ancien - $quantiteVendue;

                        $stock->update(['quantite' => $nouveau]);

                        MouvementStock::create([
                            'etablissement_id' => auth()->user()->etablissement_id,
                            'user_id' => auth()->id(),
                            'stockable_type' => Produit::class,
                            'stockable_id' => $produit->id,
                            'type' => 'sortie',
                            'quantite' => $quantiteVendue,
                            'quantite_avant' => $ancien,
                            'quantite_apres' => $nouveau,
                            'commentaire' => 'Vente Point de vente #' . $commande->numero_commande,
                            'date_mouvement' => now(),
                        ]);
                    }
                }
            }

            // Create Transaction
            Transaction::create([
                'etablissement_id' => auth()->user()->etablissement_id,
                'session_caisse_id' => $this->activeSession->id,
                'user_id' => auth()->id(),
                'montant' => $this->subtotal,
                'type' => 'vente',
                'mode_paiement' => $this->paymentMethod,
                'statut' => 'complete',
                'reference_id' => $commande->id,
                'reference_type' => Commande::class,
            ]);

            return $commande;
        });

        $this->cart = [];
        session()->flash('success', 'Vente enregistrée avec succès.');
    }

    public function render()
    {
        $etablissement_id = auth()->user()->etablissement_id;

        $categories = Categorie::where('etablissement_id', $etablissement_id)->get();

        $produits = Produit::where('etablissement_id', $etablissement_id)
            ->where('disponible', true)
            ->when($this->selectedCategory, fn($q) => $q->where('categorie_id', $this->selectedCategory))
            ->where('nom', 'like', '%' . $this->search . '%')
            ->paginate(12);

        $caissesQuery = Caisse::where('etablissement_id', $etablissement_id)
            ->where('active', true);

        // Caissiers with an assigned caisse only see their caisse
        // Admins see all caisses
        if (!auth()->user()->isAdmin() && auth()->user()->caisse_id) {
            $caissesQuery->where('id', auth()->user()->caisse_id);
        }

        $availableCaisses = $caissesQuery->get();

        return view('livewire.pages.caisses.pos', [
            'categories' => $categories,
            'produits' => $produits,
            'availableCaisses' => $availableCaisses
        ])->layout('layouts.dashboard');
    }
}
