<div class="row g-4 mb-4" wire:poll.30s>
    <style>
        .stat-card .icon.orange {
            background: rgba(var(--primary-color-rgb), 0.1) !important;
            color: var(--primary-color) !important;
        }

        /* Optional: Tint others if desired, or leave semantic */
    </style>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="icon orange">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-value">{{ $commandesToday }}</div>
            <div class="stat-label">Commandes aujourd'hui</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="icon blue">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stat-value">€{{ number_format($revenueToday, 2) }}</div>
            <div class="stat-label">Chiffre d'affaires</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="icon green">
                <i class="fas fa-table"></i>
            </div>
            <div class="stat-value">{{ $tablesOccupied }}/{{ $tablesTotal }}</div>
            <div class="stat-label">Tables occupées</div>
        </div>
    </div>
</div>