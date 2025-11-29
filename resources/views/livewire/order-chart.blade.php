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
        <canvas id="orderChart" wire:ignore></canvas>
    </div>

    <style>
        .order-chart-container {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
        }

        .chart-title i {
            color: #ff9f43;
        }

        .chart-filters {
            display: flex;
            background: #f3f4f6;
            padding: 0.25rem;
            border-radius: 10px;
            gap: 0.25rem;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: none;
            background: none;
            border-radius: 8px;
            color: #6b7280;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-btn:hover {
            background: rgba(255, 159, 67, 0.1);
            color: #ff9f43;
        }

        .filter-btn.active {
            background: white;
            color: #ff9f43;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .chart-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, #fff7ed, #fffbf5);
            border-radius: 12px;
            border-left: 4px solid #ff9f43;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #ff9f43, #ee5253);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }

        .chart-wrapper {
            flex: 1;
            position: relative;
            min-height: 250px;
        }

        canvas {
            max-height: 300px;
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            let chart = null;

            const initChart = () => {
                const ctx = document.getElementById('orderChart');
                if (!ctx) return;

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
                            borderColor: '#ff9f43',
                            backgroundColor: 'rgba(255, 159, 67, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ff9f43',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: '#ee5253',
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
                                borderColor: '#ff9f43',
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
</div>