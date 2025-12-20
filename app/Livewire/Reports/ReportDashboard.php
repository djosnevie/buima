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
    public $totalMargin = 0;
    public $opex = 0;
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

    private function getContextEtablissementId()
    {
        if (Auth::user()->isManager() && session('manager_view_site_id')) {
            return session('manager_view_site_id');
        }
        return Auth::user()->etablissement_id;
    }

    public function calculateMetrics()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();
        $etablissementId = $this->getContextEtablissementId();

        $query = Commande::where('etablissement_id', $etablissementId)
            ->whereBetween('created_at', [$start, $end]);

        if (!Auth::user()->isManager()) {
            $query->where('user_id', Auth::id());
        }

        $this->totalRevenue = $query->sum('total');
        $this->totalOrders = $query->count();
        $this->averageOrderValue = $this->totalOrders > 0 ? $this->totalRevenue / $this->totalOrders : 0;

        // Calculate Margin
        $this->totalMargin = DB::table('commande_items')
            ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
            ->where('commandes.etablissement_id', $etablissementId)
            ->whereBetween('commandes.created_at', [$start, $end])
            ->when(!Auth::user()->isManager(), function ($q) {
                return $q->where('commandes.user_id', Auth::id());
            })
            ->select(DB::raw('SUM((commande_items.prix_unitaire - produits.prix_achat) * commande_items.quantite) as margin'))
            ->value('margin') ?? 0;

        // Calculate OPEX
        $this->opex = DB::table('depenses')
            ->where('etablissement_id', $etablissementId)
            ->whereBetween('date_depense', [$start, $end])
            ->sum('montant');
    }

    private function generateReportData($type)
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();
        $etablissementId = $this->getContextEtablissementId();
        $etablissement = \App\Models\Etablissement::find($etablissementId);
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

        // $etablissementId already set above
        // $accessibleIds = Auth::user()->getAccessibleEtablissementIds(); 

        $query = Commande::where('etablissement_id', $etablissementId)
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
                $productQuery = DB::table('commande_items')
                    ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
                    ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
                    ->where('commandes.etablissement_id', $etablissementId)
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
                    ->where('commandes.etablissement_id', $etablissementId)
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
                $data['staff'] = DB::table('commandes')
                    ->join('users', 'commandes.user_id', '=', 'users.id')
                    ->where('commandes.etablissement_id', $etablissementId)
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
                    ->where('produits.etablissement_id', $etablissementId)
                    ->select('produits.nom', 'produits.prix_vente as prix', 'categories.nom as categorie', 'produits.image')
                    ->orderBy('categories.nom')
                    ->orderBy('produits.nom')
                    ->get();
                break;

            case 'caisse_sessions':
                $data['title'] = 'Rapport des Sessions de Caisse';
                $data['sessions'] = DB::table('sessions_caisse')
                    ->join('caisses', 'sessions_caisse.caisse_id', '=', 'caisses.id')
                    ->join('users', 'sessions_caisse.user_id', '=', 'users.id')
                    ->where('caisses.etablissement_id', $etablissementId)
                    ->whereBetween('sessions_caisse.date_ouverture', [$start, $end])
                    ->select('sessions_caisse.*', 'caisses.nom as caisse_nom', 'users.name as caissier')
                    ->orderBy('sessions_caisse.date_ouverture', 'desc')
                    ->get();
                break;

            case 'stock_valuation':
                $data['title'] = 'Valorisation des Stocks';
                $data['start'] = null;
                $data['end'] = null;
                $data['valuation'] = DB::table('produits')
                    ->join('stocks_produits', 'produits.id', '=', 'stocks_produits.produit_id')
                    ->where('produits.etablissement_id', $etablissementId)
                    ->select('produits.nom', 'stocks_produits.quantite', 'produits.prix_achat', DB::raw('(stocks_produits.quantite * produits.prix_achat) as total_value'))
                    ->orderByDesc('total_value')
                    ->get();
                $data['total_valuation'] = collect($data['valuation'])->sum('total_value');
                break;

            case 'sites_performance':
                // Manager Only
                if (!$user->isManager())
                    abort(403);
                $data['title'] = 'Performance par Site';
                $etablissementIds = $user->getAccessibleEtablissementIds();
                $data['sites'] = \App\Models\Etablissement::whereIn('id', $etablissementIds)
                    ->withCount([
                        'commandes' => function ($q) use ($start, $end) {
                            $q->whereBetween('created_at', [$start, $end]);
                        }
                    ])
                    ->withSum([
                        'commandes' => function ($q) use ($start, $end) {
                            $q->whereBetween('created_at', [$start, $end]);
                        }
                    ], 'total')
                    ->get()
                    ->map(function ($site) {
                        $site->average_ticket = $site->commandes_count > 0 ? $site->commandes_sum_total / $site->commandes_count : 0;
                        return $site;
                    });
                break;

            case 'hourly_sales':
                $data['title'] = 'Ventes par Heure';
                $data['hourly'] = $query->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(total) as revenue')
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get();
                break;

            case 'expenses':
                // Admin Only
                if (!$isGlobal)
                    abort(403);
                $data['title'] = 'Rapport des Dépenses';
                $data['expenses'] = DB::table('depenses')
                    ->leftJoin('categorie_depenses', 'depenses.categorie_depense_id', '=', 'categorie_depenses.id')
                    ->where('depenses.etablissement_id', $etablissementId)
                    ->whereBetween('depenses.date_depense', [$start, $end])
                    ->select('depenses.*', 'categorie_depenses.nom as categorie_nom')
                    ->orderBy('depenses.date_depense', 'desc')
                    ->get();
                $data['total_expenses'] = $this->opex;
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

    public function exportAccounting()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();
        $etablissement = Auth::user()->etablissement;

        $commandes = Commande::where('etablissement_id', $etablissement->id)
            ->whereBetween('created_at', [$start, $end])
            ->with(['items.produit', 'user'])
            ->orderBy('created_at')
            ->get();

        $filename = 'export_compta_' . $start->format('Ymd') . '_to_' . $end->format('Ymd') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($commandes) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Reference', 'Client', 'Total (TTC)', 'Taxes', 'Reduction', 'Net (HT)', 'Statut', 'Serveur']);

            foreach ($commandes as $cmd) {
                fputcsv($file, [
                    $cmd->created_at->format('d/m/Y H:i'),
                    $cmd->numero_commande,
                    $cmd->client_nom ?: 'Passant',
                    $cmd->total,
                    $cmd->montant_taxes,
                    $cmd->montant_reduction,
                    $cmd->total - $cmd->montant_taxes,
                    $cmd->statut,
                    $cmd->user->name
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.reports.report-dashboard');
        return $view->layout('layouts.dashboard');
    }
}
