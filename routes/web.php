<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

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

require __DIR__.'/auth.php';
use App\Http\Controllers\UserController;
use App\Livewire\UserManagement;


Route::middleware(['auth', 'can:manage-users'])->group(function () {
    Route::get('/users', UserManagement::class)->name('users.index');
});
use App\Livewire\Purchase\PurchaseDashboard;
use App\Livewire\Purchase\ProductManager;
use App\Livewire\Purchase\StockManager;
use App\Livewire\Purchase\SupplierManager;

Route::middleware(['auth', 'can:purchase-access'])->prefix('purchase')->group(function () {

    Route::get('/', PurchaseDashboard::class)->name('purchase.dashboard');

    Route::get('/products', ProductManager::class)->name('purchase.products');

    Route::get('/stock', StockManager::class)->name('purchase.stock');

    Route::get('/suppliers', SupplierManager::class)->name('purchase.suppliers');

});


Route::middleware(['auth', 'can:access-inkoop'])->prefix('inkoop')->group(function () {

    // Dashboard (optioneel)
    Route::get('/', function () {
        return view('livewire.purchase.dashboard');
    })->name('purchase.dashboard');

    // Productbeheer
    Route::get('/producten', ProductManager::class)->name('purchase.products');

    // Voorraadbeheer
    Route::get('/voorraad', StockManager::class)->name('purchase.stock');

    // Leveranciersoverzicht
    Route::get('/leveranciers', SupplierManager::class)->name('purchase.suppliers');

});
use App\Livewire\Finance\FinanceDashboard;
use App\Livewire\Finance\ContractManager;
use App\Livewire\Finance\InvoiceManager;

Route::middleware(['auth', 'can:access-finance'])
    ->prefix('finance')
    ->group(function () {

        // Dashboard

        // Contracten
        Route::get('/contracten', ContractManager::class)->name('finance.contracts');

        // Facturen
    });

Route::middleware(['auth'])->group(function () {

    // Finance + Manager toegang
    Route::middleware('can:access-finance')->group(function () {

        Route::get('/finance/facturen', InvoiceManager::class)
            ->name('finance.invoices');

    });

});
use App\Livewire\Maintenance\DashboardManager;
use App\Livewire\Maintenance\Dashboard;
use App\Livewire\Maintenance\Planning;
use App\Livewire\Maintenance\WorkOrders;
use App\Livewire\Maintenance\Malfunctions;
use App\Livewire\Maintenance\CreateAppointment;
use App\Livewire\Maintenance\EditWorkOrder;
use App\Livewire\Maintenance\WorkOrderForm;
use App\Livewire\Maintenance\MaintenanceDashboard;

Route::middleware(['auth', 'can:access-maintenance'])
    ->prefix('maintenance')
    ->name('maintenance.')
    ->group(function () {

        // Hoofd Dashboard
        Route::get('/', MaintenanceDashboard::class)->name('dashboard');

        // Manager Dashboard
        Route::get('/manager', DashboardManager::class)
            ->middleware('can:maintenance-manager')
            ->name('dashboard.manager');

        // Planning
        Route::get('/planning', Planning::class)
            ->name('planning');

        // Werkbon overzicht
        Route::get('/werkbonnen', WorkOrders::class)
            ->name('workorders');

        // Storingen
        Route::get('/storingen', Malfunctions::class)
            ->name('malfunctions');

        // Nieuwe afspraak (alleen manager)
        // Nieuwe afspraak (alleen manager)
Route::get('/afspraak/nieuw', CreateAppointment::class)
    ->middleware('can:maintenance-manager')
    ->name('create');


        // WERKBON: door monteurs invullen
        Route::get('/werkbon/{workOrder}', WorkOrderForm::class)
            ->name('workorder.form');

        // WERKBON: beheren (alleen manager)
        Route::get('/werkbon/{workOrder}/edit', EditWorkOrder::class)
            ->middleware('can:maintenance-manager')
            ->name('workorder.edit');
});

// Overige route
Route::get('/contact', fn() => view('contact'))->name('contact');
