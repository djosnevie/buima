<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required autofocus
                autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Password')" type="password" required
                    autocomplete="current-password" :placeholder="__('Password')" viewable />


            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <button type="submit"
                    class="w-full rounded-md px-4 py-2 text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 transition-colors duration-200"
                    style="background-color: #bf3a29 !important; color: white !important; outline: none !important;"
                    onmouseover="this.style.setProperty('background-color', '#a02f20', 'important')"
                    onmouseout="this.style.setProperty('background-color', '#bf3a29', 'important')"
                    onmousedown="this.style.setProperty('background-color', '#bf3a29', 'important'); this.style.setProperty('outline', 'none', 'important'); this.style.setProperty('box-shadow', '0 0 0 2px #bf3a29', 'important')"
                    onfocus="this.style.setProperty('background-color', '#bf3a29', 'important'); this.style.setProperty('outline', 'none', 'important'); this.style.setProperty('box-shadow', '0 0 0 2px #bf3a29', 'important')"
                    data-test="login-button">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.auth>