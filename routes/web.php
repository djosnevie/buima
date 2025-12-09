<?php

use App\Livewire\Auth\Login;
use App\Livewire\Orders\CreateOrder;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Order Management
Route::view('/orders/create', 'orders.create')
    ->middleware(['auth'])
    ->name('orders.create');

Route::get('/orders', \App\Livewire\Orders\OrderList::class)
    ->middleware(['auth'])
    ->name('orders.index');

Route::get('/orders/{commande}/invoice', function (\App\Models\Commande $commande) {
    return view('pages.orders.invoice', ['commande' => $commande]);
})->middleware(['auth'])->name('orders.invoice');

Route::middleware(['auth'])->group(function () {
    Route::get('/products', \App\Livewire\Products\ProductList::class)->name('products.index');
    Route::get('/products/create', \App\Livewire\Products\ProductForm::class)->name('products.create');
    Route::get('/products/{produit}/edit', \App\Livewire\Products\ProductForm::class)->name('products.edit');

    Route::get('/tables', \App\Livewire\Tables\TableList::class)->name('tables.index');
    Route::get('/tables/create', \App\Livewire\Tables\TableForm::class)->name('tables.create');
    Route::get('/tables/{table}/edit', \App\Livewire\Tables\TableForm::class)->name('tables.edit');
});

// Restaurant Setup
Route::get('/setup/restaurant', \App\Livewire\Setup\RestaurantCreate::class)->name('setup.restaurant');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
    Route::get('settings/restaurant', \App\Livewire\Admin\Settings\RestaurantSettings::class)->name('settings.restaurant');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
