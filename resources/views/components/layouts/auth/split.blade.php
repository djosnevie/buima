<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div
            class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-orange-600 to-red-600">
                <!-- Background image overlay -->
                <div class="absolute inset-0 bg-black/40"></div>
                <div
                    class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070')] bg-cover bg-center opacity-30">
                </div>
            </div>

            <!-- Logo du restaurant centré -->
            <div class="relative z-20 flex h-full items-center justify-center">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-4" wire:navigate>
                    <img src="{{ asset('images/logo.webp') }}" alt="{{ config('app.name', 'Laravel') }}"
                        class="w-64 h-auto object-contain drop-shadow-2xl">
                    <span
                        class="text-2xl font-bold text-white drop-shadow-lg">{{ config('app.name', 'Laravel') }}</span>
                </a>
            </div>
        </div>
        <div class="w-full lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden"
                    wire:navigate>
                    <span class="flex h-9 w-9 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>

                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>