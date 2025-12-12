<div class="order-chart-container">
    <div class="chart-header">
        <div class="chart-title">
            <i class="fas fa-chart-line me-2"></i>
            <span>Évolution des commandes</span>
        </div>
        <div class="chart-filters">
            <button wire:click="setPeriod('day')" class="filter-btn {{ $period === 'day' ? 'active' : '' }}">
                <i class="fas fa-clock"></i> Jour
            </button>
            <button wire:click="setPeriod('week')" class="filter-btn {{ $period === 'week' ? 'active' : '' }}">
                <i class="fas fa-calendar-week"></i> Semaine
            </button>
            <button wire:click="setPeriod('month')" class="filter-btn {{ $period === 'month' ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Mois
            </button>
            <button wire:click="setPeriod('year')" class="filter-btn {{ $period === 'year' ? 'active' : '' }}">
                <i class="fas fa-calendar"></i> Année
            </button>
        </div>
    </div>

    <style>
        .filter-btn.active {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }

        .filter-btn:hover:not(.active) {
            color: var(--primary-color) !important;
        }

        /* New Style for Stat Items */
        .stat-item .stat-icon {
            background: rgba(var(--primary-color-rgb), 0.1) !important;
            color: var(--primary-color) !important;
        }
    </style>

    <div class="chart-stats">
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Total</span>
                <span class="stat-value">{{ array_sum($chartData) }}</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Moyenne</span>
                <span
                    class="stat-value">{{ count($chartData) > 0 ? round(array_sum($chartData) / count($chartData), 1) : 0 }}</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Maximum</span>
                <span class="stat-value">{{ count($chartData) > 0 ? max($chartData) : 0 }}</span>
            </div>
        </div>
    </div>

    @if(!auth()->user()->isSuperAdmin())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" wire:ignore x-data="{
                chart: null,
                init() {
                    const ctx = document.getElementById('orderChart').getContext('2d');

                    // Function to init chart
                    const initChart = (data, labels) => {
                        if (this.chart) this.chart.destroy();

                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Commandes',
                                    data: data,
                                    borderColor: '#bf3a29', // Brand Primary Color
                                    backgroundColor: 'rgba(191, 58, 41, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#bf3a29',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(0,0,0,0.8)',
                                        padding: 10,
                                        cornerRadius: 8,
                                        displayColors: false,
                                        titleFont: { size: 13 },
                                        bodyFont: { size: 14, weight: 'bold' }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: '#f0f0f0',
                                            drawBorder: false
                                        },
                                        ticks: {
                                            stepSize: 1,
                                            font: { size: 11, family: 'Inter' },
                                            color: '#6c757d'
                                        }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: {
                                            font: { size: 11, family: 'Inter' },
                                            color: '#6c757d',
                                            maxRotation: 0
                                        }
                                    }
                                }
                            }
                        });
                    };

                    // Initialize with current data from Livewire
                    initChart($wire.chartData, $wire.chartLabels);

                    // Watch for updates
                    $wire.on('chartUpdated', () => {
                       initChart($wire.chartData, $wire.chartLabels);
                    });
                }
            }">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0" style="color: var(--primary-color);">
                        <i class="fas fa-chart-area me-2"></i>
                        Analyse des Commandes
                    </h5>
                </div>

                <div class="chart-container" style="position: relative; height:300px; width:100%">
                    <canvas id="orderChart"></canvas>
                </div>
            </div>

            <!-- Load Chart.js locally or CDN if preferred, but usually layout handles it. Including here just in case. -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </div>
    @endif
    <!-- Order Chart Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/order-chart.css') }}">
</div>
```