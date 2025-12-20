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
        // Create base query
        $query = CommandeItem::select(
            'commande_items.produit_id',
            'produits.etablissement_id as produit_etablissement_id', // Select this for eager load scope if needed, though 'with' closure handles it
            DB::raw('SUM(commande_items.quantite) as total_quantity'),
            DB::raw('SUM(commande_items.sous_total) as total_revenue')
        )
            ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->join('produits', 'commande_items.produit_id', '=', 'produits.id'); // Join products to get establishment if needed for filtering or data

        if ($user->isManager()) {
            $contextSiteId = session('manager_view_site_id');
            if ($contextSiteId) {
                $query->where('commandes.etablissement_id', $contextSiteId);
            } else {
                $etablissementIds = $user->getAccessibleEtablissementIds();
                $query->whereIn('commandes.etablissement_id', $etablissementIds);
            }
        } else {
            $query->where('commandes.etablissement_id', $user->etablissement_id);
            // Scope to employee if not Admin
            if (!$user->isAdmin()) {
                $query->where('commandes.user_id', $user->id);
            }
        }

        $topProducts = $query->with('produit')
            ->groupBy('commande_items.produit_id', 'produits.etablissement_id') // Group by produit and establishment to be safe per SQL mode
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.top-products', [
            'topProducts' => $topProducts
        ]);
    }
}
