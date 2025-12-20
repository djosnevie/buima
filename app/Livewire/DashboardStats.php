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

        // Check if user is a Manager (Owner) to show global stats
        if ($user->isManager()) {
            $contextSiteId = session('manager_view_site_id');

            // If a specific site is selected, use it. Otherwise use all accessible ids.
            if ($contextSiteId) {
                // Determine if user actually has access (double check, though filtered in switcher)
                // For simplified logic we assume session is valid or fallback to user's establishment if invalid?
                // Using whereIn with single ID is cleaner than separate code path
                $targetIds = [$contextSiteId];
            } else {
                $targetIds = $user->getAccessibleEtablissementIds();
            }

            // Commandes aujourd'hui (Global or Scope)
            $commandesToday = Commande::whereIn('etablissement_id', $targetIds)
                ->whereDate('created_at', Carbon::today())
                ->count();

            // Chiffre d'affaires aujourd'hui
            $revenueToday = Commande::whereIn('etablissement_id', $targetIds)
                ->whereDate('created_at', Carbon::today())
                ->whereIn('statut', ['payee', 'servie', 'livree'])
                ->sum('total');

            // Tables occupées
            $tablesOccupied = Table::whereIn('etablissement_id', $targetIds)
                ->where('statut', 'occupee')
                ->count();

            $tablesTotal = Table::whereIn('etablissement_id', $targetIds)->count();
        } else {
            // Single Establishment Logic (Admin/Employee)
            $etablissementId = $user->etablissement_id;
            $isGlobal = $user->isAdmin(); // Branch Admin sees all for that branch

            // Commandes aujourd'hui
            $cmdQuery = Commande::where('etablissement_id', $etablissementId)
                ->whereDate('created_at', Carbon::today());

            if (!$isGlobal) {
                $cmdQuery->where('user_id', $user->id);
            }
            $commandesToday = $cmdQuery->count();

            // Chiffre d'affaires aujourd'hui
            $revenueQuery = Commande::where('etablissement_id', $etablissementId)
                ->whereDate('created_at', Carbon::today())
                ->whereIn('statut', ['payee', 'servie', 'livree']);

            if (!$isGlobal) {
                $revenueQuery->where('user_id', $user->id);
            }
            $revenueToday = $revenueQuery->sum('total');

            $tablesOccupied = Table::where('etablissement_id', $etablissementId)
                ->where('statut', 'occupee')
                ->count();

            $tablesTotal = Table::where('etablissement_id', $etablissementId)->count();
        }

        return view('livewire.dashboard-stats', [
            'commandesToday' => $commandesToday,
            'revenueToday' => $revenueToday,
            'tablesOccupied' => $tablesOccupied,
            'tablesTotal' => $tablesTotal,
        ]);
    }
}
