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

    public function selectOrder($id)
    {
        $this->selectedOrderId = $id;
    }

    public function closeSideView()
    {
        $this->selectedOrderId = null;
    }

    public function deleteOrder($id)
    {
        $commande = Commande::find($id);
        if ($commande) {
            $commande->items()->delete(); // Delete items first
            $commande->delete();
            $this->closeSideView();
            session()->flash('success', 'Commande supprimée avec succès.');
        }
    }

    public function updateStatus($commandeId, $newStatus)
    {
        $commande = Commande::find($commandeId);
        if ($commande) {
            $commande->update(['statut' => $newStatus]);

            if ($newStatus === 'payee') {
                $this->dispatch('print-invoice', url: route('orders.invoice', $commande->id));

                if ($commande->table) {
                    $commande->table->markAsFree();
                }
            }

            session()->flash('success', 'Statut mis à jour avec succès.');
        }
    }

    public function render()
    {
        $query = Commande::with(['table', 'items.produit'])
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
        ])->layout('layouts.dashboard');
    }
}
