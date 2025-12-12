<?php

namespace App\Livewire;

use App\Models\CommandeItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopProducts extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Return empty if SuperAdmin
        if ($user->isSuperAdmin()) {
            return view('livewire.top-products', ['topProducts' => collect()]);
        }

        // Get top 5 products by quantity sold
        $query = CommandeItem::select('commande_items.produit_id', DB::raw('SUM(commande_items.quantite) as total_quantity'), DB::raw('SUM(commande_items.sous_total) as total_revenue'))
            ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->where('commandes.etablissement_id', $user->etablissement_id);

        // Scope to employee if not Admin
        if (!$user->isAdmin()) {
            $query->where('commandes.user_id', $user->id);
        }

        $topProducts = $query->with([
            'produit' => function ($q) use ($user) {
                $q->where('etablissement_id', $user->etablissement_id);
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
