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

    <div class="chart-wrapper">
        <canvas id="orderChart"></canvas>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            let chart = null;

            const initChart = () => {
                const ctx = document.getElementById('orderChart');
                if (!ctx) return;

                const getCssVar = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();
                const primaryColor = getCssVar('--primary-color') || '#bf3a29';
                const secondaryColor = getCssVar('--secondary-color') || '#d64a39';

                // Helper to add opacity to hex/rgb
                const addOpacity = (color, opacity) => {
                    // Simple heuristic or use canvas gradient if needed. 
                    // For now, assuming standard Chart.js behavior or using the var directly if it's hex.
                    // If var(--primary-color) is provided, we use it directly.
                    return color;
                };

                const chartData = @json($chartData);
                const chartLabels = @json($chartLabels);

                if (chart) {
                    chart.destroy();
                }

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Commandes',
                            data: chartData,
                            borderColor: primaryColor,
                            backgroundColor: 'rgba(0,0,0,0)', // Simplified or need logic for gradient
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: primaryColor,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: secondaryColor,
                            pointHoverBorderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(31, 41, 55, 0.95)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: primaryColor,
                                borderWidth: 1,
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: function (context) {
                                        return context.parsed.y + ' commande(s)';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#6b7280',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#6b7280',
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    },
                                    maxRotation: 45,
                                    minRotation: 0
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            };

            initChart();

            Livewire.on('chartUpdated', () => {
                setTimeout(() => initChart(), 100);
            });

            window.addEventListener('resize', () => {
                if (chart) {
                    chart.resize();
                }
            });
        });
    </script>
    <!-- Order Chart Styles -->
    <link rel="stylesheet" href="{{ asset('css/livewire/order-chart.css') }}">
</div>