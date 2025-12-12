<div class="auth-form-wrapper fade-in">
    <div class="text-center mb-5">
        <img src="{{ asset('images/biuma_logo_b.PNG') }}" alt="Logo" style="height: 80px; width: auto;">
    </div>

    <form wire:submit.prevent="login">
        <div class="form-floating mb-3">
            <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror"
                id="emailInput" placeholder="nom@exemple.com">
            <label for="emailInput"><i class="fas fa-user me-2"></i> Email</label>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-floating mb-4">
            <input wire:model="password" type="password" class="form-control @error('password') is-invalid @enderror"
                id="passwordInput" placeholder="Mot de passe">
            <label for="passwordInput"><i class="fas fa-lock me-2"></i> Mot de passe</label>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input wire:model="remember" class="form-check-input" type="checkbox" id="rememberCheck">
                <label class="form-check-label" for="rememberCheck">
                    Se souvenir de moi
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"
            style="background-color: #bf3a29 !important; border-color: #bf3a29 !important; outline: none !important;"
            onmouseover="this.style.setProperty('background-color', '#a02f20', 'important'); this.style.setProperty('border-color', '#a02f20', 'important');"
            onmouseout="this.style.setProperty('background-color', '#bf3a29', 'important'); this.style.setProperty('border-color', '#bf3a29', 'important');"
            onmousedown="this.style.setProperty('background-color', '#bf3a29', 'important'); this.style.setProperty('border-color', '#bf3a29', 'important'); this.style.setProperty('outline', 'none', 'important'); this.style.setProperty('box-shadow', 'none', 'important');"
            onfocus="this.style.setProperty('background-color', '#bf3a29', 'important'); this.style.setProperty('border-color', '#bf3a29', 'important'); this.style.setProperty('outline', 'none', 'important'); this.style.setProperty('box-shadow', 'none', 'important');">
            <span wire:loading.remove>Se connecter</span>
            <span wire:loading><i class="fas fa-spinner fa-spin"></i> Connexion...</span>
        </button>
    </form>
</div>