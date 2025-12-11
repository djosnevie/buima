<?php

namespace App\Livewire;

use App\Models\CommandeItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopProducts extends Component
{
    public function render()
    {
        // Get top 5 products by quantity sold
        $topProducts = CommandeItem::select('commande_items.produit_id', DB::raw('SUM(commande_items.quantite) as total_quantity'), DB::raw('SUM(commande_items.sous_total) as total_revenue'))
            ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->where('commandes.etablissement_id', auth()->user()->etablissement_id)
            ->with([
                'produit' => function ($query) {
                    // Also ensure product belongs to establishment (redundant but safe)
                    $query->where('etablissement_id', auth()->user()->etablissement_id);
                }
            ])
            ->groupBy('commande_items.produit_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.top-products', [
            'topProducts' => $topProducts
        ]);
    }
}
