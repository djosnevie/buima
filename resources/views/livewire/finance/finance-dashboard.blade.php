<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Tableau de Bord Financier</h4>
            <p class="text-muted small mb-0">Suivi des marges et de la rentabilité</p>
        </div>
        <div class="btn-group shadow-sm rounded-3 overflow-hidden">
            <button wire:click="setDateRange('today')"
                class="btn {{ $dateRange == 'today' ? 'btn-primary' : 'btn-white' }} border">Jour</button>
            <button wire:click="setDateRange('week')"
                class="btn {{ $dateRange == 'week' ? 'btn-primary' : 'btn-white' }} border">Semaine</button>
            <button wire:click="setDateRange('month')"
                class="btn {{ $dateRange == 'month' ? 'btn-primary' : 'btn-white' }} border">Mois</button>
            <button wire:click="setDateRange('year')"
                class="btn {{ $dateRange == 'year' ? 'btn-primary' : 'btn-white' }} border">Année</button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3 me-2">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="text-muted small">Chiffre d'Affaires</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ number_format($ca, 0, ',', ' ') }} <small
                            class="h6">{{ auth()->user()->etablissement->devise_display }}</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-warning-subtle text-warning rounded-3 me-2">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <span class="text-muted small">Coût Marchandises</span>
                    </div>
                    <h3 class="fw-bold mb-0">{{ number_format($cogs, 0, ',', ' ') }} <small
                            class="h6">{{ auth()->user()->etablissement->devise_display }}</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-info-subtle text-info rounded-3 me-2">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <span class="text-muted small">Marge Brute
                            ({{ $ca > 0 ? round(($grossMargin / $ca) * 100) : 0 }}%)</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($grossMargin, 0, ',', ' ') }} <small
                            class="h6">{{ auth()->user()->etablissement->devise_display }}</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div
                            class="p-2 bg-{{ $netProfit >= 0 ? 'success' : 'danger' }}-subtle text-{{ $netProfit >= 0 ? 'success' : 'danger' }} rounded-3 me-2">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <span class="text-muted small">Bénéfice Net</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-{{ $netProfit >= 0 ? 'success' : 'danger' }}">
                        {{ number_format($netProfit, 0, ',', ' ') }} <small
                            class="h6">{{ auth()->user()->etablissement->devise_display }}</small>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Évolution Revenus vs Dépenses</h6>
                    <span class="badge bg-light text-muted fw-normal">Période: {{ $dateRange }}</span>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0">Structure des Dépenses</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="expenseDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0">Rapport P&L Sommaire</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 fw-bold">Chiffre d'Affaires total</h6>
                                <p class="text-muted small mb-0">Ventes nettes (payées)</p>
                            </div>
                            <span class="h6 mb-0">{{ number_format($ca, 0, ',', ' ') }}</span>
                        </div>
                        <div class="list-group-item border-0 d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 fw-bold">Coût des Marchandises (COGS)</h6>
                                <p class="text-muted small mb-0">Basé sur le prix d'achat des produits vendus</p>
                            </div>
                            <span class="h6 mb-0 text-danger">-{{ number_format($cogs, 0, ',', ' ') }}</span>
                        </div>
                        <div
                            class="list-group-item border-0 d-flex justify-content-between align-items-center py-3 bg-light rounded-3 mt-2">
                            <div>
                                <h6 class="mb-0 fw-bold">MARGE BRUTE</h6>
                            </div>
                            <span
                                class="h5 mb-0 fw-bold text-info">{{ number_format($grossMargin, 0, ',', ' ') }}</span>
                        </div>
                        <div
                            class="list-group-item border-0 d-flex justify-content-between align-items-center py-3 mt-2">
                            <div>
                                <h6 class="mb-0 fw-bold">Dépenses Opérationnelles (OPEX)</h6>
                                <p class="text-muted small mb-0">Salaires, loyers, charges fixes...</p>
                            </div>
                            <span class="h6 mb-0 text-danger">-{{ number_format($opex, 0, ',', ' ') }}</span>
                        </div>
                        <div
                            class="list-group-item border-0 d-flex justify-content-between align-items-center py-4 border-top mt-3">
                            <div>
                                <h5 class="mb-0 fw-bold">BÉNÉFICE NET</h5>
                                <p class="text-muted small mb-0">Résultat final sur la période</p>
                            </div>
                            <span class="h4 mb-0 fw-bold text-{{ $netProfit >= 0 ? 'success' : 'danger' }}">
                                {{ number_format($netProfit, 0, ',', ' ') }}
                                {{ auth()->user()->etablissement->devise_display }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0">Répartition des Dépenses</h6>
                </div>
                <div class="card-body">
                    @forelse($expenseBreakdown as $ex)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $ex->nom ?? 'Autre' }}</span>
                                <span class="fw-bold">{{ number_format($ex->total, 0, ',', ' ') }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $opex > 0 ? ($ex->total / $opex) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                            <p>Aucune dépense sur cette période</p>
                        </div>
                    @endforelse

                    @if($opex > 0)
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>TOTAL OPEX</span>
                                <span class="text-danger">{{ number_format($opex, 0, ',', ' ') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    let financeChart = null;
    let expenseDoughnutChart = null;

    function initCharts() {
        const ctx = document.getElementById('financeChart');
        const dCtx = document.getElementById('expenseDoughnutChart');

        if (!ctx || !dCtx) return;

        if (financeChart) financeChart.destroy();
        if (expenseDoughnutChart) expenseDoughnutChart.destroy();

        financeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Revenus',
                        data: @json($revenueChartData),
                        borderColor: '#bf3a29',
                        backgroundColor: 'rgba(191, 58, 41, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Dépenses',
                        data: @json($expenseChartData),
                        borderColor: '#6b7280',
                        backgroundColor: 'rgba(107, 114, 128, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Doughnut Data
        const expenseData = @json($expenseBreakdown);
        expenseDoughnutChart = new Chart(dCtx, {
            type: 'doughnut',
            data: {
                labels: expenseData.map(d => d.nom || 'Autre'),
                datasets: [{
                    data: expenseData.map(d => d.total),
                    backgroundColor: [
                        '#bf3a29', '#ff9f43', '#17a2b8', '#ffc107', '#28a745', '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '70%'
            }
        });
    }

    setTimeout(() => {
        initCharts();
    }, 100);

    $wire.on('chartUpdated', () => {
        initCharts();
    });
</script>
@endscript
</div>