<?php

namespace App\Livewire\Orders;

use App\Models\Commande;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use WithPagination;

    public $filterStatus = 'all';
    public $search = '';

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public $selectedOrderId = null;
    public $isEditing = false;
    public $editForm = [
        'client_nom' => '',
        'client_telephone' => '',
        'type_commande' => 'sur_place',
        'table_id' => null,
        'notes' => '',
    ];

    public $productSearch = '';
    public $searchResults = [];

    public function updatedProductSearch()
    {
        if (strlen($this->productSearch) >= 2) {
            $this->searchResults = \App\Models\Produit::with('categorie')
                ->where('etablissement_id', auth()->user()->etablissement_id)
                ->where('nom', 'like', '%' . $this->productSearch . '%')
                ->take(5)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function addProductToOrder($productId)
    {
        $commande = Commande::find($this->selectedOrderId);
        if ($commande) {
            $produit = \App\Models\Produit::find($productId);
            if ($produit) {
                // Check if item exists
                $existingItem = $commande->items()->where('produit_id', $productId)->first();
                if ($existingItem) {
                    $existingItem->increment('quantite');
                } else {
                    $commande->items()->create([
                        'produit_id' => $produit->id,
                        'quantite' => 1,
                        'prix_unitaire' => $produit->prix_vente,
                        'sous_total' => $produit->prix_vente, // Initial subtotal
                    ]);
                }

                $commande->updateTotal();
                $this->productSearch = '';
                $this->searchResults = [];
                session()->flash('success', 'Produit ajouté.');
            }
        }
    }

    public function selectOrder($id)
    {
        $this->selectedOrderId = $id;
        $this->isEditing = false;
        $this->productSearch = '';
        $this->searchResults = [];
    }

    public function closeSideView()
    {
        $this->selectedOrderId = null;
        $this->isEditing = false;
        $this->productSearch = '';
        $this->searchResults = [];
    }

    public function enableEdit()
    {
        $commande = Commande::find($this->selectedOrderId);
        if ($commande) {
            $this->isEditing = true;
            $this->editForm = [
                'client_nom' => $commande->client_nom,
                'client_telephone' => $commande->client_telephone,
                'type_commande' => $commande->type_commande,
                'table_id' => $commande->table_id,
                'notes' => $commande->notes,
            ];
        }
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->productSearch = '';
        $this->searchResults = [];
    }

    public function saveOrder()
    {
        $commande = Commande::find($this->selectedOrderId);
        if ($commande) {
            $commande->update([
                'client_nom' => $this->editForm['client_nom'],
                'client_telephone' => $this->editForm['client_telephone'],
                'type_commande' => $this->editForm['type_commande'],
                'table_id' => $this->editForm['type_commande'] === 'sur_place' ? $this->editForm['table_id'] : null,
                'notes' => $this->editForm['notes'],
            ]);

            $this->isEditing = false;
            session()->flash('success', 'Commande modifiée avec succès.');
        }
    }

    public function updateItemQuantity($itemId, $qty)
    {
        // Direct update for items for simplicity in this iteration
        $item = \App\Models\CommandeItem::find($itemId);
        // Check if item belongs to selected order to be safe
        if ($item && $item->commande_id == $this->selectedOrderId) {
            if ($qty > 0) {
                $item->update(['quantite' => $qty]);
            } else {
                $item->delete();
            }
            // Trigger order total recalculation if needed (Model observer usually handles this or explicit call)
            $item->commande->updateTotal(); // Assuming this method exists or we need to trigger it. 
            // If update method doesn't exist, we might need to manually recalc:
            // $item->commande->total = $item->commande->items->sum(fn($i) => $i->prix_unitaire * $i->quantite);
            // $item->commande->save();
        }
    }

    public function removeItem($itemId)
    {
        $this->updateItemQuantity($itemId, 0);
    }

    public function deleteOrder($id)
    {
        $commande = Commande::where('etablissement_id', auth()->user()->etablissement_id)->find($id);
        if ($commande) {
            $commande->items()->delete();
            $commande->delete();
            $this->closeSideView();
            session()->flash('success', 'Commande supprimée avec succès.');
        }
    }

    public function updateStatus($commandeId, $newStatus)
    {
        $commande = Commande::where('etablissement_id', auth()->user()->etablissement_id)->find($commandeId);

        if (!$commande)
            return;

        // Lock check: if already 'payee', only allow 'annulee' (and only by admins)
        if ($commande->statut === 'payee' && $newStatus !== 'annulee') {
            session()->flash('error', 'Commande payée verrouillée. Annulation requise pour modifier.');
            return;
        }

        // Role check for cancellation
        if ($newStatus === 'annulee') {
            if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                session()->flash('error', 'Action réservée aux gérants.');
                return;
            }
        }

        $commande->update(['statut' => $newStatus]);

        if ($newStatus === 'payee') {
            $this->dispatch('print-invoice', url: route('orders.invoice', $commande->id));
            if ($commande->table) {
                $commande->table->markAsFree();
            }
        }

        session()->flash('success', 'Statut mis à jour.');
    }

    public function render()
    {
        $query = Commande::with(['table', 'items.produit'])
            ->where('etablissement_id', auth()->user()->etablissement_id)
            ->latest();

        if ($this->filterStatus !== 'all') {
            $query->where('statut', $this->filterStatus);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('numero_commande', 'like', '%' . $this->search . '%')
                    ->orWhere('client_nom', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.pages.orders.order-list', [
            'commandes' => $query->paginate(8),
            'selectedOrder' => $this->selectedOrderId ? Commande::with(['items.produit', 'table'])->find($this->selectedOrderId) : null,
            'tables' => \App\Models\Table::where('etablissement_id', auth()->user()->etablissement_id)->get(),
        ])->layout('layouts.dashboard');
    }
}
