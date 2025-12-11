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

        switch ($this->period) {
            case 'day':
                // Last 24 hours (hourly)
                for ($i = 23; $i >= 0; $i--) {
                    $startHour = Carbon::now()->subHours($i)->startOfHour();
                    $endHour = Carbon::now()->subHours($i)->endOfHour();
                    $labels[] = $startHour->format('H:00');
                    $data[] = Commande::where('etablissement_id', auth()->user()->etablissement_id)
                        ->whereBetween('created_at', [
                            $startHour,
                            $endHour
                        ])->count();
                }
                break;

            case 'week':
                // Last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $startDay = Carbon::now()->subDays($i)->startOfDay();
                    $endDay = Carbon::now()->subDays($i)->endOfDay();
                    $labels[] = $startDay->format('D d');
                    $data[] = Commande::where('etablissement_id', auth()->user()->etablissement_id)
                        ->whereBetween('created_at', [
                            $startDay,
                            $endDay
                        ])->count();
                }
                break;

            case 'month':
                // Last 30 days
                for ($i = 29; $i >= 0; $i--) {
                    $startDay = Carbon::now()->subDays($i)->startOfDay();
                    $endDay = Carbon::now()->subDays($i)->endOfDay();
                    $labels[] = $startDay->format('d M');
                    $data[] = Commande::where('etablissement_id', auth()->user()->etablissement_id)
                        ->whereBetween('created_at', [
                            $startDay,
                            $endDay
                        ])->count();
                }
                break;

            case 'year':
                // Last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $startMonth = Carbon::now()->subMonths($i)->startOfMonth();
                    $endMonth = Carbon::now()->subMonths($i)->endOfMonth();
                    $labels[] = $startMonth->format('M Y');
                    $data[] = Commande::where('etablissement_id', auth()->user()->etablissement_id)
                        ->whereBetween('created_at', [
                            $startMonth,
                            $endMonth
                        ])->count();
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
