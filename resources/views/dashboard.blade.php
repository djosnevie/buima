@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    @livewire('dashboard-stats')

    <div class="row g-4">
        <div class="col-md-8">
            <div class="stat-card" style="height: 400px;">
                <h5 class="mb-3">Commandes récentes</h5>
                <p class="text-muted">
                    <i class="fas fa-chart-line me-2 text-warning"></i>
                    Graphique des commandes à venir
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="height: 400px;">
                <h5 class="mb-3">Produits populaires</h5>
                <p class="text-muted">
                    <i class="fas fa-fire me-2 text-danger"></i>
                    Top ventes
                </p>
            </div>
        </div>
    </div>
@endsection