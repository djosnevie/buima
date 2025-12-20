<?php

namespace App\Livewire\Finance;

use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Depense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FinanceDashboard extends Component
{
    public $dateRange = 'month';
    public $startDate, $endDate;

    public $ca = 0;
    public $cogs = 0;
    public $opex = 0;
    public $grossMargin = 0;
    public $netProfit = 0;

    // Chart Data
    public $revenueChartData = [];
    public $expenseChartData = [];
    public $chartLabels = [];

    public function mount()
    {
        $this->setDateRange('month');
    }

    public function setDateRange($range)
    {
        $this->dateRange = $range;
        switch ($range) {
            case 'today':
                $this->startDate = Carbon::today()->startOfDay();
                $this->endDate = Carbon::today()->endOfDay();
                break;
            case 'week':
                $this->startDate = Carbon::now()->startOfWeek();
                $this->endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth();
                $this->endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $this->startDate = Carbon::now()->startOfYear();
                $this->endDate = Carbon::now()->endOfYear();
                break;
        }
        $this->calculateFinance();
    }

    public function calculateFinance()
    {
        $accessibleIds = auth()->user()->getAccessibleEtablissementIds();

        // 1. Revenue (CA) - Only paid orders
        $this->ca = Commande::whereIn('etablissement_id', $accessibleIds)
            ->where('statut', 'payee')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->sum('total');

        // 2. COGS (Cost of Goods Sold)
        $this->cogs = DB::table('commande_items')
            ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
            ->whereIn('commandes.etablissement_id', $accessibleIds)
            ->where('commandes.statut', 'payee')
            ->whereBetween('commandes.created_at', [$this->startDate, $this->endDate])
            ->select(DB::raw('SUM(commande_items.quantite * produits.prix_achat) as total_cogs'))
            ->value('total_cogs') ?? 0;

        // 3. OPEX (Operating Expenses)
        $this->opex = Depense::whereIn('etablissement_id', $accessibleIds)
            ->whereBetween('date_depense', [$this->startDate, $this->endDate])
            ->sum('montant');

        $this->grossMargin = $this->ca - $this->cogs;
        $this->netProfit = $this->grossMargin - $this->opex;

        $this->generateChartData();
    }

    public function generateChartData()
    {
        $accessibleIds = auth()->user()->getAccessibleEtablissementIds();
        $this->revenueChartData = [];
        $this->expenseChartData = [];
        $this->chartLabels = [];

        // Logic based on dateRange
        $points = 0;
        $format = '';
        $subUnit = '';

        switch ($this->dateRange) {
            case 'today':
                $points = 24;
                $format = 'H:00';
                $subUnit = 'hour';
                break;
            case 'week':
                $points = 7;
                $format = 'D d';
                $subUnit = 'day';
                break;
            case 'month':
                $points = 30;
                $format = 'd M';
                $subUnit = 'day';
                break;
            case 'year':
                $points = 12;
                $format = 'M Y';
                $subUnit = 'month';
                break;
        }

        for ($i = $points - 1; $i >= 0; $i--) {
            $date = Carbon::now();
            if ($this->dateRange == 'today')
                $date->subHours($i);
            elseif ($this->dateRange == 'week')
                $date->subDays($i);
            elseif ($this->dateRange == 'month')
                $date->subDays($i);
            elseif ($this->dateRange == 'year')
                $date->subMonths($i);

            $this->chartLabels[] = $date->translatedFormat($format);

            $start = (clone $date)->startOf($subUnit);
            $end = (clone $date)->endOf($subUnit);

            $rev = Commande::whereIn('etablissement_id', $accessibleIds)
                ->where('statut', 'payee')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total');

            $exp = Depense::whereIn('etablissement_id', $accessibleIds)
                ->whereBetween('date_depense', [$start, $end])
                ->sum('montant');

            $this->revenueChartData[] = $rev;
            $this->expenseChartData[] = $exp;
        }
    }

    public function render()
    {
        $accessibleIds = auth()->user()->getAccessibleEtablissementIds();

        // Breakdown of expenses by category
        $expenseBreakdown = DB::table('depenses')
            ->leftJoin('categorie_depenses', 'depenses.categorie_depense_id', '=', 'categorie_depenses.id')
            ->whereIn('depenses.etablissement_id', $accessibleIds)
            ->whereBetween('depenses.date_depense', [$this->startDate, $this->endDate])
            ->select('categorie_depenses.nom', DB::raw('SUM(depenses.montant) as total'))
            ->groupBy('categorie_depenses.nom')
            ->get();

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.finance.finance-dashboard', [
            'expenseBreakdown' => $expenseBreakdown
        ]);
        return $view->layout('layouts.dashboard');
    }
}
