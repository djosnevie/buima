@section('title', 'Sauvegardes & Sécurité')

<div class="container-fluid px-4">
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Historique des Sauvegardes</h5>
                    <button wire:click="createBackup" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i> Créer une sauvegarde
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Nom du fichier</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3 text-end">Taille</th>
                                    <th class="px-4 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $backup)
                                    <tr>
                                        <td class="px-4 py-3 fw-bold">{{ $backup['name'] }}</td>
                                        <td class="px-4 py-3">{{ $backup['date'] }}</td>
                                        <td class="px-4 py-3 text-end">{{ $backup['size'] }}</td>
                                        <td class="px-4 py-3 text-end">
                                            <button class="btn btn-sm btn-light border" title="Télécharger"><i
                                                    class="fas fa-download"></i></button>
                                            <button wire:click="deleteBackup('{{ $backup['name'] }}')"
                                                class="btn btn-sm btn-light border text-danger" title="Supprimer"><i
                                                    class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-shield-alt fa-3x mb-3 opacity-25"></i>
                                            <p>Aucune sauvegarde trouvée. Commencez par en créer une.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white mb-4">
                <div class="card-body p-4 text-center">
                    <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                    <h5 class="fw-bold">Sauvegarde Automatique</h5>
                    <p class="small mb-4">Vos données sont précieuses. Activez la sauvegarde quotidienne sur le cloud.
                    </p>
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input" type="checkbox" role="switch" checked disabled>
                        <label class="form-check-label ms-2">Automatique (Actif)</label>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i> À propos des sauvegardes</h6>
                    <p class="small text-muted">Les sauvegardes incluent :</p>
                    <ul class="small text-muted ps-3">
                        <li>Base de données complète</li>
                        <li>Logos et images produits</li>
                        <li>Paramètres de l'établissement</li>
                    </ul>
                    <hr>
                    <p class="small mb-0 text-warning"><i class="fas fa-exclamation-triangle me-1"></i> Il est
                        recommandé de télécharger vos sauvegardes régulièrement sur un support externe.</p>
                </div>
            </div>
        </div>
    </div>
</div>