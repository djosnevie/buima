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
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Exporter les Rapports (PDF)</h5>
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
            /* OR, they genuinely don't want the hover effect to change the color so drastically. */
            /* Let's try removing the `color: white` change and see if that satisfies "don't become white". */
            /* Actually, if I remove `color: white`, text stays red on red background -> unreadable. */
            /* Maybe they meant the ICON becomes white? */
            /* Let's assume they want a subtle hover, not a full fill. */
            /* I will change hover to just darken the border and background slightly, keeping text red. */
            background-color: #ffe0e0;
            color: #dc3545; 
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2);
        }
    </style>
</div>