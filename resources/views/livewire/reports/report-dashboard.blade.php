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
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-circle bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-coins fa-lg"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($totalRevenue, 0, ',', ' ') }} <small class="text-muted fs-6">{{ auth()->user()->etablissement->devise }}</small></h2>
                    <p class="text-muted small mb-0">Total Recettes</p>
                </div>
            </div>
        </div>

        <!-- Margin -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-circle bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-dollar-sign fa-lg"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1 text-info">{{ number_format($totalMargin, 0, ',', ' ') }} <small class="text-muted fs-6">{{ auth()->user()->etablissement->devise }}</small></h2>
                    <p class="text-muted small mb-0">Marge Estimeé</p>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-shopping-bag fa-lg"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalOrders }}</h2>
                    <p class="text-muted small mb-0">Commandes</p>
                </div>
            </div>
        </div>

        <!-- Avg Order -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1">{{ number_format($averageOrderValue, 0, ',', ' ') }} <small class="text-muted fs-6">{{ auth()->user()->etablissement->devise }}</small></h2>
                    <p class="text-muted small mb-0">Panier Moyen</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    @if($previewData)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Aperçu : {{ $previewData['title'] }}</h5>
                        <div class="d-flex align-items-center gap-2">
                             <button wire:click="downloadReport('{{ $previewType }}')"
                                wire:loading.attr="disabled"
                                class="btn btn-primary btn-sm px-4 shadow-sm">
                                <span wire:loading.remove wire:target="downloadReport">
                                    <i class="fas fa-file-pdf me-2"></i> Télécharger
                                </span>
                                <span wire:loading wire:target="downloadReport">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Génération...
                                </span>
                            </button>
                            <button wire:click="$set('previewData', null)" class="btn-close"></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Display simple preview based on type -->
                        @if($previewType === 'sales')
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Commandes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['daily_sales'] as $sale)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                                            <td class="text-end">{{ number_format($sale->total, 0, ',', ' ') }}
                                                {{ auth()->user()->etablissement->devise }}</td>
                                            <td class="text-end">{{ $sale->count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($previewType === 'products')
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-end">Qté</th>
                                        <th class="text-end">Revenu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['products'] as $prod)
                                        <tr>
                                            <td>{{ $prod->nom }}</td>
                                            <td class="text-end">{{ $prod->qty }}</td>
                                            <td class="text-end">{{ number_format($prod->revenue, 0, ',', ' ') }}
                                                {{ auth()->user()->etablissement->devise }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($previewType === 'product_list')
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Catégorie</th>
                                        <th>Produit</th>
                                        <th class="text-end">Prix</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['product_list'] as $prod)
                                        <tr>
                                            <td>{{ $prod->categorie }}</td>
                                            <td>{{ $prod->nom }}</td>
                                            <td class="text-end">{{ number_format($prod->prix, 0, ',', ' ') }}
                                                {{ auth()->user()->etablissement->devise }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                         @elseif($previewType === 'categories')
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Catégorie</th>
                                        <th class="text-end">Ventes</th>
                                        <th class="text-end">Revenu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['categories'] as $cat)
                                        <tr>
                                            <td>{{ $cat->nom }}</td>
                                            <td class="text-end">{{ $cat->count }}</td>
                                            <td class="text-end">{{ number_format($cat->revenue, 0, ',', ' ') }}
                                                {{ auth()->user()->etablissement->devise }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($previewType === 'staff')
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Serveur</th>
                                        <th class="text-end">Commandes</th>
                                        <th class="text-end">Revenu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['staff'] as $st)
                                        <tr>
                                            <td>{{ $st->name }}</td>
                                            <td class="text-end">{{ $st->count }}</td>
                                            <td class="text-end">{{ number_format($st->revenue, 0, ',', ' ') }}
                                                {{ auth()->user()->etablissement->devise }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($previewType === 'payment')
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Statut</th>
                                        <th class="text-end">Nombre</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['payments'] as $pay)
                                        <tr>
                                            <td>{{ ucfirst($pay->statut) }}</td>
                                            <td class="text-end">{{ $pay->count }}</td>
                                            <td class="text-end">{{ number_format($pay->total, 0, ',', ' ') }}
                                                {{ auth()->user()->etablissement->devise }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($previewType === 'caisse_sessions')
                            @php $devise = auth()->user()->etablissement->devise_display ?? 'FC'; @endphp
                            @foreach($previewData['sessions'] as $sess)
                                @php
                                    $statut = $sess->statut;
                                    $ecart = ($sess->montant_fermeture_reel ?? 0) - ($sess->montant_fermeture_theorique ?? 0);
                                @endphp
                                <div class="card mb-3 border-0 shadow-sm rounded-3">
                                    <div class="card-header d-flex justify-content-between align-items-center py-2 bg-light rounded-top-3">
                                        <div>
                                            <span class="fw-bold me-2"><i class="fas fa-cash-register me-1 text-primary"></i>{{ $sess->caisse_nom }}</span>
                                            <span class="text-muted small me-2"><i class="fas fa-user me-1"></i>{{ $sess->caissier }}</span>
                                            <span class="text-muted small"><i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($sess->date_ouverture)->format('d/m/Y H:i') }}</span>
                                            @if($sess->date_fermeture)
                                                <span class="text-muted small"> → {{ \Carbon\Carbon::parse($sess->date_fermeture)->format('d/m/Y H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $statut === 'ouverte' ? 'bg-success' : 'bg-secondary' }}">{{ strtoupper($statut) }}</span>
                                            <a href="{{ route('caisses.sessions.print-global', $sess->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Imprimer Global"><i class="fas fa-print"></i> Global</a>
                                            <a href="{{ route('caisses.sessions.print-food', $sess->id) }}" target="_blank" class="btn btn-sm btn-outline-warning" title="Imprimer Food"><i class="fas fa-hamburger"></i> Food</a>
                                            <a href="{{ route('caisses.sessions.print-drinks', $sess->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Imprimer Boissons"><i class="fas fa-glass-martini"></i> Boissons</a>
                                        </div>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="row g-3">
                                            {{-- KPIs --}}
                                            <div class="col-md-7">
                                                <div class="row g-2 text-center">
                                                    <div class="col-4">
                                                        <div class="rounded-3 p-2 h-100" style="background:#f0fdf4;">
                                                            <div class="small text-muted">Food</div>
                                                            <div class="fw-bold text-success">{{ number_format($sess->food_total, 0, ',', ' ') }} <small>{{ $devise }}</small></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="rounded-3 p-2 h-100" style="background:#eff6ff;">
                                                            <div class="small text-muted">Boissons</div>
                                                            <div class="fw-bold text-info">{{ number_format($sess->drinks_total, 0, ',', ' ') }} <small>{{ $devise }}</small></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="rounded-3 p-2 h-100" style="background:#fef9c3;">
                                                            <div class="small text-muted">CA Total</div>
                                                            <div class="fw-bold text-warning">{{ number_format($sess->ca_total, 0, ',', ' ') }} <small>{{ $devise }}</small></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="rounded-3 p-2 h-100" style="background:#f8f9fa;">
                                                            <div class="small text-muted">Fond d'ouverture</div>
                                                            <div class="fw-bold">{{ number_format($sess->montant_ouverture, 0, ',', ' ') }} <small>{{ $devise }}</small></div>
                                                        </div>
                                                    </div>
                                                    @if($sess->date_fermeture)
                                                    <div class="col-4">
                                                        <div class="rounded-3 p-2 h-100" style="background:#f8f9fa;">
                                                            <div class="small text-muted">Réel Compté</div>
                                                            <div class="fw-bold">{{ number_format($sess->montant_fermeture_reel, 0, ',', ' ') }} <small>{{ $devise }}</small></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="rounded-3 p-2 h-100" style="background: {{ $ecart >= 0 ? '#f0fdf4' : '#fff1f2' }}">
                                                            <div class="small text-muted">Écart</div>
                                                            <div class="fw-bold {{ $ecart >= 0 ? 'text-success' : 'text-danger' }}">
                                                                {{ $ecart >= 0 ? '+' : '' }}{{ number_format($ecart, 0, ',', ' ') }} <small>{{ $devise }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Per-server breakdown --}}
                                            <div class="col-md-5">
                                                <div class="small text-muted fw-semibold mb-1"><i class="fas fa-users me-1"></i>Ventes par Serveur</div>
                                                @forelse($sess->servers as $srv)
                                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                                        <span class="small">{{ $srv->name }}</span>
                                                        <span class="fw-bold small">{{ number_format($srv->total, 0, ',', ' ') }} {{ $devise }}</span>
                                                    </div>
                                                @empty
                                                    <div class="text-muted small fst-italic">Aucune vente enregistrée.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @elseif($previewType === 'stock_valuation')
                            <div class="alert alert-info py-2 mb-3">
                                Total Valorisation: <strong>{{ number_format($previewData['total_valuation'], 0, ',', ' ') }} {{ auth()->user()->etablissement->devise }}</strong>
                            </div>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-end">Stock</th>
                                        <th class="text-end">Prix Achat</th>
                                        <th class="text-end">Valorisation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['valuation'] as $val)
                                        <tr>
                                            <td>{{ $val->nom }}</td>
                                            <td class="text-end">{{ $val->quantite }}</td>
                                            <td class="text-end">{{ number_format($val->prix_achat, 0, ',', ' ') }}</td>
                                            <td class="text-end fw-bold">{{ number_format($val->total_value, 0, ',', ' ') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($previewType === 'expenses')
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Catégorie</th>
                                        <th>Description</th>
                                        <th class="text-end">Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['expenses'] as $exp)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($exp->date_depense)->format('d/m/Y') }}</td>
                                            <td>{{ $exp->categorie_nom }}</td>
                                            <td>{{ $exp->description }}</td>
                                            <td class="text-end text-danger">-{{ number_format($exp->montant, 0, ',', ' ') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($previewType === 'hourly_sales')
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Heure</th>
                                        <th class="text-end">Commandes</th>
                                        <th class="text-end">Revenu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['hourly'] as $hour)
                                        <tr>
                                            <td>{{ sprintf('%02d:00', $hour->hour) }} - {{ sprintf('%02d:00', $hour->hour + 1) }}</td>
                                            <td class="text-end">{{ $hour->count }}</td>
                                            <td class="text-end">{{ number_format($hour->revenue, 0, ',', ' ') }}
                                                {{ auth()->user()->etablissement->devise }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($previewType === 'sites_performance')
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Site</th>
                                        <th class="text-end">Commandes</th>
                                        <th class="text-end">Revenu</th>
                                        <th class="text-end">Panier Moyen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData['sites'] as $site)
                                        <tr>
                                            <td>{{ $site->nom }}</td>
                                            <td class="text-end">{{ $site->commandes_count }}</td>
                                            <td class="text-end">{{ number_format($site->commandes_sum_total ?? 0, 0, ',', ' ') }}
                                                {{ auth()->user()->etablissement->devise }}</td>
                                            <td class="text-end">{{ number_format($site->average_ticket, 0, ',', ' ') }}
                                                {{ auth()->user()->etablissement->devise }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Export Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Exporter les Rapports (PDF)</h5>
                    <button wire:click="exportAccounting" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                        <i class="fas fa-file-csv me-1"></i> Export Excel (Compta)
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Ventes -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <button wire:click="previewReport('sales')" class="report-card w-100 bg-white">
                                <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                                <span class="fw-bold">Ventes</span>
                            </button>
                        </div>
                        <!-- Produits Top -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <button wire:click="previewReport('products')" class="report-card w-100 bg-white">
                                <i class="fas fa-hamburger fa-2x mb-2"></i>
                                <span class="fw-bold">Produits Top</span>
                            </button>
                        </div>
                        <!-- Liste Produits -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <button wire:click="previewReport('product_list')" class="report-card w-100 bg-white">
                                <i class="fas fa-list fa-2x mb-2"></i>
                                <span class="fw-bold">Liste Produits</span>
                            </button>
                        </div>
                        <!-- Catégories -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <button wire:click="previewReport('categories')" class="report-card w-100 bg-white">
                                <i class="fas fa-tags fa-2x mb-2"></i>
                                <span class="fw-bold">Catégories</span>
                            </button>
                        </div>
                        <!-- Performance -->
                        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                            <div class="col-6 col-md-4 col-lg-2">
                                <button wire:click="previewReport('staff')" class="report-card w-100 bg-white">
                                    <i class="fas fa-users-cog fa-2x mb-2"></i>
                                    <span class="fw-bold">Performance</span>
                                </button>
                            </div>
                        @endif
                        <!-- Paiements -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <button wire:click="previewReport('payment')" class="report-card w-100 bg-white">
                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                <span class="fw-bold">Paiements</span>
                            </button>
                        </div>
                        <!-- Caisse Sessions -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <button wire:click="previewReport('caisse_sessions')" class="report-card w-100 bg-white">
                                <i class="fas fa-cash-register fa-2x mb-2"></i>
                                <span class="fw-bold">Sessions Caisse</span>
                            </button>
                        </div>
                        <!-- Stock Valuation -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <button wire:click="previewReport('stock_valuation')" class="report-card w-100 bg-white">
                                <i class="fas fa-boxes fa-2x mb-2"></i>
                                <span class="fw-bold">Valeur Stock</span>
                            </button>
                        </div>
                        <!-- Expenses -->

                        
                        <!-- Hourly Sales -->
                        <div class="col-6 col-md-4 col-lg-2">
                             <button wire:click="previewReport('hourly_sales')" class="report-card w-100 bg-white">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <span class="fw-bold">Heures</span>
                            </button>
                        </div>

                         <!-- Multi-Site Performance (Manager Only) -->
                         @if(auth()->user()->isManager())
                            <div class="col-6 col-md-4 col-lg-2">
                                <button wire:click="previewReport('sites_performance')" class="report-card w-100 bg-white">
                                    <i class="fas fa-network-wired fa-2x mb-2"></i>
                                    <span class="fw-bold">Performances Sites</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            color: #fff; /* Keeping white text on red background is actually correct design-wise, perhaps the user meant the background turned white? */
            /* Wait, the user said "buttons becomes white". */
            /* The CSS says background-color: #dc3545 (red) and color: white. */
            /* If the user dislikes it, maybe they want the inverse or just a shadow? */
            /* "in hover repports buttons becomes white ? I don;t want that." */
            /* The initial state is white bg (from Bootstrap utility classes on the button). */
            /* The CSS class .report-card sets background: #fff5f5 (light pink). */
            /* On hover it goes red. */
            /* Let's re-read: "becomes white". */
            /* Inspecting the blade: <button ... class="report-card w-100 bg-white"> */
            /* Ah! The inline class `bg-white` might be conflicting or interacting weirdly. */
            /* The user likely means they don't want the text to disappear or the whole thing to look 'white' / 'blank' if something is wrong. */
            background-color: #dc3545 !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2);
        }
        
        .report-card:hover i, .report-card:hover span {
            color: #ffffff !important;
        }
    </style>
</div>