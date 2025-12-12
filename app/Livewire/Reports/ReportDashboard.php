<?php

namespace App\Livewire\Reports;

use App\Models\Commande;
use App\Models\User;
use App\Models\Produit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReportDashboard extends Component
{
    public $dateRange = 'today';
    public $startDate;
    public $endDate;

    public $totalRevenue = 0;
    public $totalOrders = 0;
    public $averageOrderValue = 0;

    public $previewData = null;
    public $previewType = null;

    public function mount()
    {
        $this->setDateRange('today');
    }

    public function setDateRange($range)
    {
        $this->dateRange = $range;
        switch ($range) {
            case 'today':
                $this->startDate = Carbon::today()->startOfDay()->format('Y-m-d');
                $this->endDate = Carbon::today()->endOfDay()->format('Y-m-d');
                break;
            case 'week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
        }

        $this->calculateMetrics();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'startDate' || $propertyName === 'endDate') {
            $this->dateRange = 'custom';
            $this->previewData = null; // Close preview on date change
            $this->calculateMetrics();
        }
    }

    public function updatedDateRange($value)
    {
        $this->previewData = null; // Close preview on date change
        $this->setDateRange($value);
    }

    public function calculateMetrics()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $query = Commande::where('etablissement_id', Auth::user()->etablissement_id)
            ->whereBetween('created_at', [$start, $end]);

        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $this->totalRevenue = $query->sum('total');
        $this->totalOrders = $query->count();
        $this->averageOrderValue = $this->totalOrders > 0 ? $this->totalRevenue / $this->totalOrders : 0;
    }

    private function generateReportData($type)
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();
        $etablissement = Auth::user()->etablissement;
        $user = Auth::user();
        $isGlobal = $user->isAdmin() || $user->isSuperAdmin();

        // Prevent non-admins from accessing staff reports
        if ($type === 'staff' && !$isGlobal) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'type' => $type,
            'start' => $start->format('d/m/Y'),
            'end' => $end->format('d/m/Y'),
            'etablissement' => $etablissement,
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $query = Commande::where('etablissement_id', $etablissement->id)
            ->whereBetween('created_at', [$start, $end]);

        if (!$isGlobal) {
            $query->where('user_id', $user->id);
        }

        switch ($type) {
            case 'sales':
                $data['title'] = 'Rapport des Ventes';
                $data['daily_sales'] = $query->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                $data['total_revenue'] = $this->totalRevenue;
                break;

            case 'products':
                $data['title'] = 'Top Produits';
                // This requires a join with items
                // Need to re-apply the user filter on the join query
                $productQuery = DB::table('commande_items')
                    ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
                    ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
                    ->where('commandes.etablissement_id', $etablissement->id)
                    ->whereBetween('commandes.created_at', [$start, $end]);

                if (!$isGlobal) {
                    $productQuery->where('commandes.user_id', $user->id);
                }

                $data['products'] = $productQuery->select('produits.nom', DB::raw('SUM(commande_items.quantite) as qty'), DB::raw('SUM(commande_items.sous_total) as revenue'))
                    ->groupBy('produits.nom')
                    ->orderByDesc('qty')
                    ->limit(20)
                    ->get();
                break;

            case 'categories':
                $data['title'] = 'Ventes par Catégorie';
                $catQuery = DB::table('commande_items')
                    ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
                    ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
                    ->join('categories', 'produits.categorie_id', '=', 'categories.id')
                    ->where('commandes.etablissement_id', $etablissement->id)
                    ->whereBetween('commandes.created_at', [$start, $end]);

                if (!$isGlobal) {
                    $catQuery->where('commandes.user_id', $user->id);
                }

                $data['categories'] = $catQuery->select('categories.nom', DB::raw('SUM(commande_items.sous_total) as revenue'), DB::raw('COUNT(commande_items.id) as count'))
                    ->groupBy('categories.nom')
                    ->orderByDesc('revenue')
                    ->get();
                break;

            case 'staff':
                $data['title'] = 'Performance Serveurs';
                // Only accessible by admin (checked above)
                $data['staff'] = DB::table('commandes')
                    ->join('users', 'commandes.user_id', '=', 'users.id')
                    ->where('commandes.etablissement_id', $etablissement->id)
                    ->whereBetween('commandes.created_at', [$start, $end])
                    ->select('users.name', DB::raw('COUNT(commandes.id) as count'), DB::raw('SUM(commandes.total) as revenue'))
                    ->groupBy('users.name')
                    ->orderByDesc('revenue')
                    ->get();
                break;

            case 'payment':
                $data['title'] = 'Méthodes de Paiement';
                $data['payments'] = $query->select('statut', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
                    ->groupBy('statut')
                    ->get();
                break;

            case 'product_list':
                $data['title'] = 'Liste des Produits';
                $data['start'] = null;
                $data['end'] = null;
                $data['product_list'] = DB::table('produits')
                    ->leftJoin('categories', 'produits.categorie_id', '=', 'categories.id')
                    ->where('produits.etablissement_id', $etablissement->id)
                    ->select('produits.nom', 'produits.prix_vente as prix', 'categories.nom as categorie', 'produits.image')
                    ->orderBy('categories.nom')
                    ->orderBy('produits.nom')
                    ->get();
                break;
        }

        if (!$isGlobal) {
            $data['title'] .= ' - ' . $user->name;
        }

        return $data;
    }

    public function previewReport($type)
    {
        $this->previewType = $type;
        $this->previewData = $this->generateReportData($type);
    }

    public function downloadReport($type)
    {
        $data = $this->generateReportData($type);
        $pdf = Pdf::loadView('reports.pdf.generic', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'rapport_' . $type . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function render()
    {
        return view('livewire.reports.report-dashboard')->layout('layouts.dashboard');
    }
}
