<div class="auth-form-wrapper fade-in">
    <div class="text-center mb-5">
        <img src="{{ asset('images/logo.webp') }}" alt="Logo" style="height: 80px; width: auto;">
    </div>

    <form wire:submit.prevent="login">
        <div class="form-floating mb-4">
            <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror" id="emailInput" placeholder="name@example.com">
            <label for="emailInput"><i class="fas fa-user me-2"></i> Email</label>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-floating mb-5">
            <input wire:model="password" type="password" class="form-control @error('password') is-invalid @enderror" id="passwordInput" placeholder="Password">
            <label for="passwordInput"><i class="fas fa-lock me-2"></i> Password</label>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary mb-4">
            <span wire:loading.remove>Se connecter</span>
            <span wire:loading>Loading...</span>
        </button>
    </form>
</div>