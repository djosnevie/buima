<?php

namespace App\Livewire;

use App\Models\Commande;
use Livewire\Component;
use Carbon\Carbon;

class OrderChart extends Component
{
    public $period = 'day'; // day, week, month, year
    public $chartData = [];
    public $chartLabels = [];

    public function mount()
    {
        $this->updateChartData();
    }

    public function setPeriod($period)
    {
        $this->period = $period;
        $this->updateChartData();
        $this->dispatch('chartUpdated');
    }

    public function updateChartData()
    {
        $data = [];
        $labels = [];

        $user = auth()->user();

        // SuperAdmin sees nothing/map (handled in view or returns empty)
        if ($user->isSuperAdmin()) {
            return;
        }

        $isGlobal = $user->isAdmin();
        $etablissementId = $user->etablissement_id;

        switch ($this->period) {
            case 'day':
                // Use whereDate and groupBy HOUR for strict alignment with DashboardStats
                $query = Commande::where('etablissement_id', $etablissementId)
                    ->whereDate('created_at', Carbon::today());

                if (!$isGlobal) {
                    $query->where('user_id', $user->id);
                }

                // Execute query grouped by hour
                $results = $query->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                    ->groupBy('hour')
                    ->pluck('count', 'hour');

                // Fill 24 hours
                for ($i = 0; $i < 24; $i++) {
                    // Format explicitly 00:00, 01:00...
                    $labels[] = sprintf('%02d:00', $i);
                    $data[] = $results->get($i, 0);
                }
                break;

            case 'week':
                // Last 7 days including today? Or aligned to week?
                // DashboardStats doesn't have a weekly view, but consistency is key.
                // Keeping existing loop logic but ensuring endOfDay covers correct range, 
                // OR use DATE(created_at). Grouping by DATE is safer.

                // Let's use 7 days back range
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();

                $query = Commande::where('etablissement_id', $etablissementId)
                    ->whereBetween('created_at', [$startDate, $endDate]);

                if (!$isGlobal) {
                    $query->where('user_id', $user->id);
                }

                $results = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->pluck('count', 'date');

                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $dateString = $date->toDateString();
                    $labels[] = $date->format('D d');
                    $data[] = $results->get($dateString, 0);
                }
                break;

            case 'month':
                // Last 30 days
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();

                $query = Commande::where('etablissement_id', $etablissementId)
                    ->whereBetween('created_at', [$startDate, $endDate]);

                if (!$isGlobal) {
                    $query->where('user_id', $user->id);
                }

                $results = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->pluck('count', 'date');

                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $dateString = $date->toDateString();
                    $labels[] = $date->format('d M');
                    $data[] = $results->get($dateString, 0);
                }
                break;

            case 'year':
                // Last 12 months
                $startDate = Carbon::now()->subMonths(11)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();

                $query = Commande::where('etablissement_id', $etablissementId)
                    ->whereBetween('created_at', [$startDate, $endDate]);

                if (!$isGlobal) {
                    $query->where('user_id', $user->id);
                }

                // Group by Year-Month
                $results = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                    ->groupBy('month')
                    ->pluck('count', 'month');

                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $monthString = $date->format('Y-m');
                    $labels[] = $date->format('M Y');
                    $data[] = $results->get($monthString, 0);
                }
                break;
        }

        $this->chartData = $data;
        $this->chartLabels = $labels;
    }

    public function render()
    {
        return view('livewire.order-chart');
    }
}
