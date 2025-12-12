<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Rapports & Statistiques</h2>
            <p class="text-muted">Analysez les performances de votre restaurant.</p>
        </div>

        <!-- Date Filter -->
        <div class="d-flex gap-2">
            <select wire:model.live="dateRange" class="form-select fw-bold border-0 shadow-sm" style="width: 150px;">
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette Semaine</option>
                <option value="month">Ce Mois</option>
                <option value="year">Cette Année</option>
                <option value="custom">Personnalisé</option>
            </select>
            @if($dateRange === 'custom')
                <input wire:model.live="startDate" type="date" class="form-control border-0 shadow-sm">
                <input wire:model.live="endDate" type="date" class="form-control border-0 shadow-sm">
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <!-- Revenue -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-circle bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-coins fa-lg"></i>
                        </div>
                        <div class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                            Revenus
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($totalRevenue, 0, ',', ' ') }} <small
                            class="text-muted fs-6">{{ auth()->user()->etablissement->devise }}</small></h2>
                    <p class="text-muted small mb-0">Total sur la période</p>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-shopping-bag fa-lg"></i>
                        </div>
                        <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                            Commandes
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalOrders }}</h2>
                    <p class="text-muted small mb-0">Commandes passées</p>
                </div>
            </div>
        </div>

        <!-- Avg Order -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <div class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">
                            Panier Moyen
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($averageOrderValue, 0, ',', ' ') }} <small
                            class="text-muted fs-6">{{ auth()->user()->etablissement->devise }}</small></h2>
                    <p class="text-muted small mb-0">Moyenne par commande</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Exporter les Rapports (PDF)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Ventes -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="#"
                                onclick="printReport('{{ route('reports.print', ['type' => 'sales', 'range' => $dateRange, 'start' => $startDate, 'end' => $endDate]) }}'); return false;"
                                class="report-card">
                                <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                                <span class="fw-bold">Ventes</span>
                            </a>
                        </div>
                        <!-- Produits Top -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="#"
                                onclick="printReport('{{ route('reports.print', ['type' => 'products', 'range' => $dateRange, 'start' => $startDate, 'end' => $endDate]) }}'); return false;"
                                class="report-card">
                                <i class="fas fa-hamburger fa-2x mb-2"></i>
                                <span class="fw-bold">Produits Top</span>
                            </a>
                        </div>
                        <!-- Liste Produits -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="#"
                                onclick="printReport('{{ route('reports.print', ['type' => 'product_list', 'range' => $dateRange, 'start' => $startDate, 'end' => $endDate]) }}'); return false;"
                                class="report-card">
                                <i class="fas fa-list fa-2x mb-2"></i>
                                <span class="fw-bold">Liste Produits</span>
                            </a>
                        </div>
                        <!-- Catégories -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="#"
                                onclick="printReport('{{ route('reports.print', ['type' => 'categories', 'range' => $dateRange, 'start' => $startDate, 'end' => $endDate]) }}'); return false;"
                                class="report-card">
                                <i class="fas fa-tags fa-2x mb-2"></i>
                                <span class="fw-bold">Catégories</span>
                            </a>
                        </div>
                        <!-- Performance -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="#"
                                onclick="printReport('{{ route('reports.print', ['type' => 'staff', 'range' => $dateRange, 'start' => $startDate, 'end' => $endDate]) }}'); return false;"
                                class="report-card">
                                <i class="fas fa-users-cog fa-2x mb-2"></i>
                                <span class="fw-bold">Performance</span>
                            </a>
                        </div>
                        <!-- Paiements -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="#"
                                onclick="printReport('{{ route('reports.print', ['type' => 'payment', 'range' => $dateRange, 'start' => $startDate, 'end' => $endDate]) }}'); return false;"
                                class="report-card">
                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                <span class="fw-bold">Paiements</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Iframe for Printing -->
    <iframe id="printFrame" style="display:none;"></iframe>

    <style>
        .report-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            border: 1px solid #dc3545;
            border-radius: 0.5rem;
            text-decoration: none;
            color: #dc3545;
            transition: all 0.3s ease;
            height: 100%;
            background: #fff5f5;
        }

        .report-card:hover {
            background-color: #dc3545;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2);
        }
    </style>

    <script>
        function printReport(url) {
            const iframe = document.getElementById('printFrame');
            iframe.src = url;
            // The iframe page (generic.blade.php) has window.onload = window.print()
        }
    </script>
</div>