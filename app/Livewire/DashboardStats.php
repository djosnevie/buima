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
        // Commandes aujourd'hui
        $commandesToday = Commande::whereDate('created_at', Carbon::today())->count();

        // Chiffre d'affaires aujourd'hui
        $revenueToday = Commande::whereDate('created_at', Carbon::today())
            ->whereIn('statut', ['payee', 'servie'])
            ->sum('total');

        // Tables occupées
        $tablesOccupied = Table::where('statut', 'occupee')->count();
        $tablesTotal = Table::count();

        return view('livewire.dashboard-stats', [
            'commandesToday' => $commandesToday,
            'revenueToday' => $revenueToday,
            'tablesOccupied' => $tablesOccupied,
            'tablesTotal' => $tablesTotal,
        ]);
    }
}
