<?php

namespace App\Livewire\Pages\Public;

use App\Models\Etablissement;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Table;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Menu extends Component
{
    public $slug;
    public $tableNumber;
    public $etablissement;
    public $cart = [];
    public $activeCategory = null;
    public $showCart = false;

    public function mount($slug, $table = null)
    {
        $this->slug = $slug;
        $this->tableNumber = $table;
        $this->etablissement = Etablissement::where('slug', $slug)->firstOrFail();

        // Load initial category
        $firstCat = $this->etablissement->categories()->first();
        $this->activeCategory = $firstCat ? $firstCat->id : null;
    }

    public function addToCart($productId, $name, $price)
    {
        $produit = \App\Models\Produit::find($productId);
        if (!$produit)
            return;

        $currentQty = isset($this->cart[$productId]) ? $this->cart[$productId]['quantity'] : 0;

        if (!$produit->hasSufficientStock($currentQty + 1)) {
            session()->flash('error', "Stock insuffisant pour {$name}.");
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'name' => $name,
                'price' => $price,
                'quantity' => 1
            ];
        }
        $this->dispatch('cart-updated');
    }

    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']--;
            if ($this->cart[$productId]['quantity'] <= 0) {
                unset($this->cart[$productId]);
            }
        }
    }

    public function getCartTotalProperty()
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function submitOrder()
    {
        if (empty($this->cart))
            return;

        $tableId = null;
        if ($this->tableNumber) {
            $table = Table::where('etablissement_id', $this->etablissement->id)
                ->where('numero', $this->tableNumber)
                ->first();
            $tableId = $table ? $table->id : null;
        }

        // Final stock check
        foreach ($this->cart as $id => $item) {
            $produit = \App\Models\Produit::find($id);
            if ($produit && !$produit->hasSufficientStock($item['quantity'])) {
                session()->flash('error', "Stock insuffisant pour {$item['name']}.");
                return;
            }
        }

        $commande = Commande::create([
            'etablissement_id' => $this->etablissement->id,
            'table_id' => $tableId,
            'user_id' => $this->etablissement->users()->where('role', 'admin')->first()->id ?? 1,
            'statut' => 'en_attente', // QR orders start as pending
            'total' => $this->cartTotal,
            'numero_commande' => Commande::generateOrderNumber('QR'),
        ]);

        foreach ($this->cart as $id => $item) {
            CommandeItem::create([
                'commande_id' => $commande->id,
                'produit_id' => $id,
                'quantite' => $item['quantity'],
                'prix_unitaire' => $item['price'],
                'sous_total' => $item['price'] * $item['quantity'],
            ]);
        }

        $this->cart = [];
        $this->showCart = false;

        session()->flash('order_placed', 'Votre commande a été envoyée ! Un serveur viendra confirmer avec vous.');
    }

    public function render()
    {
        $categories = $this->etablissement->categories()->with('produits')->get();
        return view('livewire.pages.public.menu', [
            'categories' => $categories
        ])->layout('layouts.app'); // Public layout
    }
}
