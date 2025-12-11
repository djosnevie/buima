<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 fw-bold">Paramètres de l'Établissement</h2>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit.prevent="updateSettings">
        <div class="row g-4">

            <!-- Left Column: Branding -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <h5 class="card-title fw-bold mb-4">Identité Visuelle</h5>

                        <!-- Logo Upload -->
                        <div class="mb-4 position-relative d-inline-block">
                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}" class="rounded-circle shadow-sm" width="120"
                                    height="120" style="object-fit: cover; border: 4px solid white;">
                            @elseif($currentLogo)
                                <img src="{{ asset('storage/' . $currentLogo) }}" class="rounded-circle shadow-sm"
                                    width="120" height="120" style="object-fit: cover; border: 4px solid white;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto"
                                    style="width: 120px; height: 120px; border: 4px solid white;">
                                    <i class="fas fa-store fa-3x text-muted"></i>
                                </div>
                            @endif
                            <label for="logo-settings-upload"
                                class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm p-2 cursor-pointer border"
                                style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-camera text-secondary"></i>
                                <input wire:model="logo" id="logo-settings-upload" type="file" class="d-none">
                            </label>
                        </div>
                        @error('logo') <div class="text-danger small">{{ $message }}</div> @enderror


                    </div>
                </div>
            </div>

            <!-- Right Column: Settings -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h5 class="card-title fw-bold mb-0">Informations & Configuration</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nom" class="form-label fw-medium">Nom du Restaurant</label>
                                <input wire:model="nom" type="text" class="form-control form-control-lg" id="nom"
                                    placeholder="Ex: O'Menu Paris">
                                @error('nom') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label fw-medium">Type de Service</label>
                                <select wire:model="type" class="form-select" id="type">
                                    <option value="mixte">Mixte (Sur place & Emporter)</option>
                                    <option value="avec_tables">Avec Tables (Service à table)</option>
                                    <option value="sans_tables">Emporter / Livraison Uniquement</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="devise" class="form-label fw-medium">Devise</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-coins"></i></span>
                                    <select wire:model="devise"
                                        class="form-select @error('devise') is-invalid @enderror" id="devise">
                                        <option value="XAF">FCFA (XAF)</option>
                                        <option value="EUR">Euro (€)</option>
                                        <option value="USD">Dollar ($)</option>
                                        <option value="GBP">Livre (£)</option>
                                        <option value="CAD">Dollar Canadien (CAD)</option>
                                    </select>
                                </div>
                                @error('devise') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-medium">Email de Contact</label>
                                <input wire:model="email" type="email" class="form-control" id="email">
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="telephone" class="form-label fw-medium">Téléphone</label>
                                <input wire:model="telephone" type="text" class="form-control" id="telephone">
                            </div>

                            <div class="col-md-12">
                                <label for="adresse" class="form-label fw-medium">Adresse Postale</label>
                                <textarea wire:model="adresse" class="form-control" id="adresse" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-top text-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>