<?php

namespace App\Livewire;

use App\Models\Commande;
use App\Models\Table;
use Livewire\Component;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Return empty stats for SuperAdmin
        if ($user->isSuperAdmin()) {
            return view('livewire.dashboard-stats', [
                'commandesToday' => 0,
                'revenueToday' => 0,
                'tablesOccupied' => 0,
                'tablesTotal' => 0,
            ]);
        }

        $etablissementId = $user->etablissement_id;
        // Ensure Admin (Gerant) has global view. Explicitly check role or helper.
        $isGlobal = ($user->role === 'admin' || $user->isSuperAdmin());

        // Commandes aujourd'hui
        $cmdQuery = Commande::where('etablissement_id', $etablissementId)
            ->whereDate('created_at', Carbon::today());

        if (!$isGlobal) {
            $cmdQuery->where('user_id', $user->id);
        }

        $commandesToday = $cmdQuery->count();

        // Chiffre d'affaires aujourd'hui
        // Include 'livree' as it represents completed sales for delivery orders
        $revenueQuery = Commande::where('etablissement_id', $etablissementId)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('statut', ['payee', 'servie', 'livree']);

        if (!$isGlobal) {
            $revenueQuery->where('user_id', $user->id);
        }

        $revenueToday = $revenueQuery->sum('total');

        // Tables occupées (Tables are establishment-wide, usually visible to all staff)
        // But if strict scoping is needed, employees might only see tables they are serving?
        // Usually tablestatus is global. Leaving table counts global for now unless requested otherwise,
        // but since the prompt said "employees list in function of him", maybe tables are exempt or should remain global context?
        // "si le user connecte est un employe alors qu'il lui affiche la liste en focntion de lui" applies typically to orders/sales.
        $tablesOccupied = Table::where('etablissement_id', $etablissementId)
            ->where('statut', 'occupee')
            ->count();

        $tablesTotal = Table::where('etablissement_id', $etablissementId)->count();

        return view('livewire.dashboard-stats', [
            'commandesToday' => $commandesToday,
            'revenueToday' => $revenueToday,
            'tablesOccupied' => $tablesOccupied,
            'tablesTotal' => $tablesTotal,
        ]);
    }
}
