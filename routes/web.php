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

// Public Menu
Route::get('/m/{slug}/{table?}', \App\Livewire\Pages\Public\Menu::class)->name('public.menu');
Route::get('/payment/{commandeId}', \App\Livewire\Pages\Public\PaymentProcess::class)->name('public.payment');

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Modular Protected Routes
Route::middleware(['auth'])->group(function () {
    // Orders
    Route::middleware(['module:orders'])->group(function () {
        Route::view('/orders/create', 'orders.create')
            ->middleware('caisse_session')
            ->name('orders.create');
        Route::get('/orders/{commande}/edit', function (\App\Models\Commande $commande) {
            return view('orders.create', ['orderId' => $commande->id]);
        })->middleware('caisse_session')->name('orders.edit');
        Route::get('/orders', \App\Livewire\Orders\OrderList::class)->name('orders.index');
        Route::get('/orders/{commande}/invoice', function (\App\Models\Commande $commande) {
            return view('pages.orders.invoice', ['commande' => $commande]);
        })->name('orders.invoice');
        
        Route::get('/orders/{commande}/kitchen-ticket', function (\App\Models\Commande $commande) {
            $type = request('type');
            $itemsToPrint = [];

            foreach ($commande->items as $item) {
                if ($item->quantite > $item->quantite_imprimee) {
                    // Vérifier le filtre de type (cuisine = pas boisson, bar = boisson)
                    $produitType = $item->produit->type ?? 'autre';
                    $isBar = $produitType === 'boisson';
                    
                    if ($type === 'cuisine' && $isBar) continue;
                    if ($type === 'bar' && !$isBar) continue;

                    // Calcul du delta
                    $delta = $item->quantite - $item->quantite_imprimee;

                    // Marquer comme imprimé D'ABORD, pour ne pas injecter quantite_a_imprimer dans le SQL
                    $item->update(['quantite_imprimee' => $item->quantite]);
                    
                    // Ensuite on attache la propriété pour la vue
                    $item->quantite_a_imprimer = $delta;
                    $itemsToPrint[] = $item;
                }
            }

            $isReprint = false;

            if (empty($itemsToPrint)) {
                $isReprint = true;
                foreach ($commande->items as $item) {
                    $produitType = $item->produit->type ?? 'autre';
                    $isBar = $produitType === 'boisson';
                    
                    if ($type === 'cuisine' && $isBar) continue;
                    if ($type === 'bar' && !$isBar) continue;

                    $item->quantite_a_imprimer = $item->quantite;
                    $itemsToPrint[] = $item;
                }
                
                if (empty($itemsToPrint)) {
                    return '<script>window.close();</script>';
                }
            }

            return view('pages.orders.kitchen-ticket', [
                'commande' => $commande,
                'itemsToPrint' => collect($itemsToPrint),
                'isReprint' => $isReprint
            ]);
        })->name('orders.kitchen-ticket');
    });

    // Products
    Route::middleware(['module:products'])->group(function () {
        Route::get('/products', \App\Livewire\Products\ProductList::class)->name('products.index');
        Route::get('/products/create', \App\Livewire\Products\ProductForm::class)->name('products.create');
        Route::get('/products/{produit}/edit', \App\Livewire\Products\ProductForm::class)->name('products.edit');
        Route::get('/categories', \App\Livewire\Products\CategoryManager::class)->name('categories.index');
    });

    // Tables
    Route::middleware(['module:tables'])->group(function () {
        Route::get('/tables', \App\Livewire\Tables\TableList::class)->name('tables.index');
        Route::get('/tables/create', \App\Livewire\Tables\TableForm::class)->name('tables.create');
        Route::get('/tables/{table}/edit', \App\Livewire\Tables\TableForm::class)->name('tables.edit');
    });

    // Inventory (Suppliers & Stock)
    Route::middleware(['module:inventory'])->group(function () {
        Route::get('/suppliers', \App\Livewire\Suppliers\SupplierList::class)->name('suppliers.index');
        Route::get('/suppliers/create', \App\Livewire\Suppliers\SupplierForm::class)->name('suppliers.create');
        Route::get('/suppliers/{id}/edit', \App\Livewire\Suppliers\SupplierForm::class)->name('suppliers.edit');
        Route::get('/stock', \App\Livewire\Stock\StockDashboard::class)->name('stock.index');
    });

    // Caisses
    Route::middleware(['module:caisses'])->group(function () {
        Route::get('/caisses', \App\Livewire\Caisses\CaisseList::class)->name('caisses.index');
        Route::get('/caisses/{id}/sessions', \App\Livewire\Caisses\CaisseSessionManager::class)->name('caisses.sessions');
        Route::get('/caisses/sessions/{session}/print-global', [\App\Http\Controllers\CaisseSessionPrintController::class, 'printGlobal'])->name('caisses.sessions.print-global');
        Route::get('/caisses/sessions/{session}/print-food', [\App\Http\Controllers\CaisseSessionPrintController::class, 'printFood'])->name('caisses.sessions.print-food');
        Route::get('/caisses/sessions/{session}/print-drinks', [\App\Http\Controllers\CaisseSessionPrintController::class, 'printDrinks'])->name('caisses.sessions.print-drinks');
    });

    // POS
    Route::middleware(['module:pos'])->get('/pos', \App\Livewire\Caisses\POS::class)->name('pos.index');

    // Finance
    Route::middleware(['module:finance'])->group(function () {
        Route::get('/finance', \App\Livewire\Finance\FinanceDashboard::class)->name('finance.index');
        Route::get('/finance/expenses', \App\Livewire\Finance\DepenseManager::class)->name('finance.expenses');
        Route::get('/finance/categories', \App\Livewire\Finance\CategorieDepenseManager::class)->name('finance.categories');
    });

    // Reports
    Route::middleware(['module:reports'])->group(function () {
        Route::get('/reports', \App\Livewire\Reports\ReportDashboard::class)->name('reports.index');
        Route::get('/reports/print/{type}', [\App\Http\Controllers\ReportPrintingController::class, 'show'])->name('reports.print');
    });
});

// Restaurant Setup
Route::get('/setup/restaurant', \App\Livewire\Setup\RestaurantCreate::class)->name('setup.restaurant');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::middleware(['is_admin'])->group(function () {
        Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
        Volt::route('settings/password', 'settings.password')->name('user-password.edit');
        Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
        Route::get('settings/restaurant', \App\Livewire\Admin\Settings\RestaurantSettings::class)->name('settings.restaurant');
        Route::get('settings/backups', \App\Livewire\Admin\Settings\BackupSettings::class)->name('settings.backups');

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

        // Section Management
        Route::get('settings/sections', \App\Livewire\Admin\Sections\SectionManager::class)->name('settings.sections');

        // User Management
        Route::get('settings/users', \App\Livewire\Admin\Users\UserManager::class)->name('settings.users');

        // Points de Vente (Multi-site for Managers)
        Route::get('/points-de-vente', \App\Livewire\Admin\POS\PointDeVenteManager::class)
            ->middleware('module:pos')
            ->name('manager.pos.index');
    });

    // Super Admin Dashboard
    Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard\SuperAdminDashboard::class)
        ->middleware(['is_super_admin'])
        ->name('super_admin.dashboard');

    // Super Admin – Manager Management
    Route::get('/admin/managers', \App\Livewire\Admin\Dashboard\ManagerManager::class)
        ->middleware(['is_super_admin'])
        ->name('super_admin.managers');
});
