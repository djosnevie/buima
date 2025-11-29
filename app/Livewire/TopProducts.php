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
        $topProducts = CommandeItem::select('produit_id', DB::raw('SUM(quantite) as total_quantity'), DB::raw('SUM(sous_total) as total_revenue'))
            ->with('produit')
            ->groupBy('produit_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.top-products', [
            'topProducts' => $topProducts
        ]);
    }
}
