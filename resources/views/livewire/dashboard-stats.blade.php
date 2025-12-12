@if(!auth()->user()->isSuperAdmin())
    <div>
        <div class="row g-4 mb-4">
            <!-- ... (rest of blade content) - actually I should wrap the whole file content -->
            <!-- Card 1: Commandes Today -->
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="icon-circle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px; background-color: rgba(191, 58, 41, 0.1); color: #bf3a29 !important;">
                                <i class="fas fa-shopping-bag fa-lg"></i>
                            </div>
                            <span class="badge rounded-pill px-3 py-2"
                                style="background-color: rgba(191, 58, 41, 0.1); color: #bf3a29;">
                                Aujourd'hui
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-1 display-6">{{ $commandesToday }}</h3>
                            <p class="text-muted small mb-0">Nouvelles Commandes</p>
                        </div>
                        <div class="position-absolute bottom-0 end-0 me-3 mb-2" style="opacity: 0.1;">
                            <i class="fas fa-shopping-bag fa-4x text-primary transform-rotate"
                                style="color: #bf3a29 !important;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Revenue Today -->
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="icon-circle text-success rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px; background-color: rgba(25, 135, 84, 0.1);">
                                <i class="fas fa-coins fa-lg"></i>
                            </div>
                            <span class="badge text-success rounded-pill px-3 py-2"
                                style="background-color: rgba(25, 135, 84, 0.1);">
                                Revenus
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-1 display-6">{{ number_format($revenueToday, 0, ',', ' ') }}
                                <span
                                    class="fs-5 text-muted">{{ auth()->user()->etablissement->devise_display ?? 'FCFA' }}</span>
                            </h3>
                            <p class="text-muted small mb-0">Chiffre d'Affaires</p>
                        </div>
                        <div class="position-absolute bottom-0 end-0 me-3 mb-2" style="opacity: 0.1;">
                            <i class="fas fa-chart-line fa-4x text-success transform-rotate"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Tables Occupied -->
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="icon-circle text-warning rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px; background-color: rgba(255, 193, 7, 0.1);">
                                <i class="fas fa-chair fa-lg"></i>
                            </div>
                            <span class="badge text-warning rounded-pill px-3 py-2"
                                style="background-color: rgba(255, 193, 7, 0.1);">
                                En Service
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-1 display-6">{{ $tablesOccupied }} / {{ $tablesTotal }}</h3>
                            <p class="text-muted small mb-0">Tables Occupées</p>
                        </div>
                        <div class="position-absolute bottom-0 end-0 me-3 mb-2" style="opacity: 0.1;">
                            <i class="fas fa-chair fa-4x text-warning transform-rotate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .icon-circle {
                transition: all 0.3s ease;
            }

            .transform-rotate {
                transform: rotate(-15deg);
            }

            .hover-scale {
                transition: transform 0.2s ease-in-out;
            }

            .hover-scale:hover {
                transform: scale(1.02);
            }
        </style>
    </div>
@endif