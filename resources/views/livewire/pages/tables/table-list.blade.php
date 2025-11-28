@section('title', 'Gestion des Tables')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Tables</li>
@endsection

<div class="tables-management">
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="header-actions">
        <h1>Tables</h1>
        <a href="{{ route('tables.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Nouvelle Table
        </a>
    </div>

    <div class="tables-grid">
        @forelse($tables as $table)
            <div class="table-card status-{{ $table->statut }}">
                <div class="table-header">
                    <span class="table-number">T-{{ $table->numero }}</span>
                    <div class="table-actions">
                        <a href="{{ route('tables.edit', $table->id) }}" class="btn-icon edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button wire:click="delete({{ $table->id }})" wire:confirm="Supprimer cette table ?"
                            class="btn-icon delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="table-body">
                    <div class="capacity">
                        <i class="fas fa-users"></i>
                        <span>{{ $table->capacite }} pers.</span>
                    </div>
                    <div class="status-badge">
                        {{ ucfirst($table->statut) }}
                    </div>
                </div>

                <div class="table-footer">
                    <button wire:click="toggleStatus({{ $table->id }})"
                        class="btn-toggle {{ $table->statut === 'libre' ? 'btn-occupy' : 'btn-free' }}">
                        @if($table->statut === 'libre')
                            <i class="fas fa-lock"></i> Occuper
                        @else
                            <i class="fas fa-unlock"></i> Libérer
                        @endif
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-chair"></i>
                <p>Aucune table configurée</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">
        {{ $tables->links() }}
    </div>
    <style>
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .btn-add {
            background: linear-gradient(135deg, #ff9f43, #ee5253);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 159, 67, 0.3);
            color: white;
        }

        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .table-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            border-top: 4px solid #e5e7eb;
        }

        .table-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .table-card.status-libre {
            border-top-color: #10b981;
        }

        .table-card.status-occupee {
            border-top-color: #ef4444;
        }

        .table-card.status-reservee {
            border-top-color: #f59e0b;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .table-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }

        .table-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn-icon.edit {
            background: #eff6ff;
            color: #3b82f6;
        }

        .btn-icon.edit:hover {
            background: #3b82f6;
            color: white;
        }

        .btn-icon.delete {
            background: #fef2f2;
            color: #ef4444;
        }

        .btn-icon.delete:hover {
            background: #ef4444;
            color: white;
        }

        .table-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .capacity {
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            background: #f3f4f6;
            color: #4b5563;
        }

        .status-libre .status-badge {
            background: #dcfce7;
            color: #166534;
        }

        .status-occupee .status-badge {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-reservee .status-badge {
            background: #fef3c7;
            color: #92400e;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Table Footer */
        .table-footer {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #f3f4f6;
        }

        .btn-toggle {
            width: 100%;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-occupy {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-occupy:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
        }

        .btn-free {
            background: #dcfce7;
            color: #16a34a;
        }

        .btn-free:hover {
            background: #16a34a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(22, 163, 74, 0.3);
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        :deep(.pagination) {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
            margin: 0;
            align-items: center;
        }

        :deep(.page-item) {
            display: inline-block;
        }

        :deep(.page-link) {
            min-width: 40px;
            height: 40px;
            padding: 0.5rem 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.3s ease;
            background: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        :deep(.page-link:hover) {
            background: #fff7ed;
            border-color: #ff9f43;
            color: #ff9f43;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 159, 67, 0.2);
        }

        :deep(.page-item.active .page-link) {
            background: linear-gradient(135deg, #ff9f43, #ee5253);
            border-color: #ff9f43;
            color: white;
            box-shadow: 0 4px 12px rgba(255, 159, 67, 0.4);
        }

        :deep(.page-item.disabled .page-link) {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f9fafb;
        }

        :deep(.page-item.disabled .page-link:hover) {
            transform: none;
            box-shadow: none;
            border-color: #e5e7eb;
        }
    </style>
</div>