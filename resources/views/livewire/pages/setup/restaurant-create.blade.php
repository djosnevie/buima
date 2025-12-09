<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">Configurer votre Restaurant</h3>
                    <p class="text-muted">Commencez avec O'Menu en quelques secondes.</p>
                </div>

                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="createRestaurant">

                    <h5 class="mb-3 border-bottom pb-2">Informations du Restaurant</h5>

                    <!-- Nom -->
                    <div class="mb-3">
                        <label for="nom_restaurant" class="form-label">Nom du Restaurant</label>
                        <input wire:model="nom_restaurant" type="text"
                            class="form-control @error('nom_restaurant') is-invalid @enderror" id="nom_restaurant">
                        @error('nom_restaurant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Type & Devise Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="type_restaurant" class="form-label">Type d'Établissement</label>
                            <select wire:model="type_restaurant"
                                class="form-select @error('type_restaurant') is-invalid @enderror" id="type_restaurant">
                                <option value="mixte">Mixte (Sur place & Emporter)</option>
                                <option value="avec_tables">Restauration sur place uniquement</option>
                                <option value="sans_tables">Emporter / Livraison uniquement</option>
                            </select>
                            @error('type_restaurant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="devise" class="form-label">Devise Principale</label>
                            <select wire:model="devise" class="form-select @error('devise') is-invalid @enderror"
                                id="devise">
                                <option value="XAF">FCFA (XAF)</option>
                                <option value="EUR">Euro (€)</option>
                                <option value="USD">Dollar ($)</option>
                            </select>
                            @error('devise') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="theme_color" class="form-label">Couleur du Thème</label>
                            <input wire:model="theme_color" type="color" class="form-control form-control-color w-100"
                                id="theme_color" title="Choisir une couleur">
                            @error('theme_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Email & Phone Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email_restaurant" class="form-label">Email Restaurant (Facultatif)</label>
                            <input wire:model="email_restaurant" type="email" class="form-control"
                                id="email_restaurant">
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input wire:model="telephone" type="text" class="form-control" id="telephone">
                        </div>
                    </div>

                    <!-- Logo -->
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <div class="d-flex align-items-center gap-3">
                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}" class="rounded-circle" width="50" height="50"
                                    style="object-fit: cover;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <input wire:model="logo" type="file"
                                    class="form-control form-control-sm @error('logo') is-invalid @enderror">
                                @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div class="mb-4">
                        <label for="adresse" class="form-label">Adresse Complète</label>
                        <textarea wire:model="adresse" class="form-control" id="adresse" rows="2"></textarea>
                    </div>

                    @guest
                        <h5 class="mb-3 border-bottom pb-2 mt-4">Compte Administrateur</h5>

                        <!-- Admin Name & Email -->
                        <div class="mb-3">
                            <label for="nom_admin" class="form-label">Nom Complet</label>
                            <input wire:model="nom_admin" type="text"
                                class="form-control @error('nom_admin') is-invalid @enderror" id="nom_admin">
                            @error('nom_admin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email_admin" class="form-label">Adresse Email (Login)</label>
                            <input wire:model="email_admin" type="email"
                                class="form-control @error('email_admin') is-invalid @enderror" id="email_admin">
                            @error('email_admin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Password Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input wire:model="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" id="password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <input wire:model="password_confirmation" type="password" class="form-control"
                                    id="password_confirmation">
                            </div>
                        </div>
                    @endguest

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg"
                            style="background-color: {{ $theme_color }}; border-color: {{ $theme_color }}">Créer mon
                            Restaurant</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>