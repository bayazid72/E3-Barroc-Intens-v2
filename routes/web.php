<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Controllers & Livewire Components
use App\Http\Controllers\UserController;

use App\Livewire\UserManagement;

use App\Livewire\Purchase\PurchaseDashboard;
use App\Livewire\Purchase\ProductManager;
use App\Livewire\Purchase\StockManager;
use App\Livewire\Purchase\SupplierManager;

use App\Livewire\Finance\FinanceDashboard;
use App\Livewire\Finance\ContractManager;
use App\Livewire\Finance\InvoiceManager;

use App\Livewire\Maintenance\DashboardManager;
use App\Livewire\Maintenance\Dashboard;
use App\Livewire\Maintenance\Planning;
use App\Livewire\Maintenance\WorkOrders;
use App\Livewire\Maintenance\Malfunctions;
use App\Livewire\Maintenance\CreateAppointment;
use App\Livewire\Maintenance\EditWorkOrder;
use App\Livewire\Maintenance\WorkOrderForm;
use App\Livewire\Maintenance\MaintenanceDashboard;
use App\Models\LoginAttempt;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('home');

Route::get('/contact', fn() => view('contact'))->name('contact');


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Settings (Profile, Password, 2FA)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(when(
            Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
            ['password.confirm'],
            [],
        ))
        ->name('two-factor.show');
    Route::get('admin/login-logs', function () {

        $logs = LoginAttempt::with('user')
            ->latest()
            ->paginate(50);

        return view('livewire.auth.login-logs', compact('logs'));
    })->name('admin.login-logs');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Fortify)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| User Management (Admins)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:manage-users'])->group(function () {
    Route::get('/users', UserManagement::class)->name('users.index');
});


/*
|--------------------------------------------------------------------------
| Purchase (Inkoop)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:purchase-access'])
    ->prefix('purchase')
    ->name('purchase.')
    ->group(function () {
        Route::get('/', PurchaseDashboard::class)->name('dashboard');
        Route::get('/products', ProductManager::class)->name('products');
        Route::get('/stock', StockManager::class)->name('stock');
        Route::get('/suppliers', SupplierManager::class)->name('suppliers');
        
        // Paid invoices management
        Route::get('/paid-invoices', \App\Livewire\Purchase\PaidInvoicesManager::class)->name('paid-invoices.index');
        Route::get('/paid-invoices/{invoice}', \App\Livewire\Purchase\PaidInvoiceDetail::class)->name('paid-invoices.show');
    });


// Alternatieve Nederlandse routes (inkoop)
Route::middleware(['auth', 'can:access-inkoop'])
    ->prefix('inkoop')
    ->name('purchase.')
    ->group(function () {
        Route::get('/', PurchaseDashboard::class)->name('dashboard');
        Route::get('/producten', ProductManager::class)->name('products');
        Route::get('/voorraad', StockManager::class)->name('stock');
        Route::get('/leveranciers', SupplierManager::class)->name('suppliers');
        
        // Paid invoices management (Dutch)
        Route::get('/betaalde-facturen', \App\Livewire\Purchase\PaidInvoicesManager::class)->name('paid-invoices.index');
        Route::get('/betaalde-facturen/{invoice}', \App\Livewire\Purchase\PaidInvoiceDetail::class)->name('paid-invoices.show');
    });


/*
|--------------------------------------------------------------------------
| Finance (Financieel)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:access-finance'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {
        Route::get('/contracten', ContractManager::class)->name('contracts');
        Route::get('/facturen', InvoiceManager::class)->name('invoices');
        Route::get('/facturen/{invoice}', \App\Livewire\Finance\InvoiceShow::class)->name('invoices.show');
        
        // Quote routes
        Route::get('/offertes', \App\Livewire\Finance\QuoteList::class)->name('quotes.index');
        Route::get('/offertes/{quote}', \App\Livewire\Finance\QuoteShow::class)->name('quotes.show');
    });


/*
|--------------------------------------------------------------------------
| Maintenance (Onderhoud)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:access-maintenance'])
    ->prefix('maintenance')
    ->name('maintenance.')
    ->group(function () {

        // Dashboards
        Route::get('/', MaintenanceDashboard::class)->name('dashboard');
        Route::get('/manager', DashboardManager::class)
            ->middleware('can:maintenance-manager')
            ->name('dashboard.manager');

        // Planning
        Route::get('/planning', Planning::class)->name('planning');

        // Werkbonnen
        Route::get('/werkbonnen', WorkOrders::class)->name('workorders');
        Route::get('/werkbon/{workOrder}', WorkOrderForm::class)->name('workorder.form');
        Route::get('/werkbon/{workOrder}/edit', EditWorkOrder::class)
            ->middleware('can:maintenance-manager')
            ->name('workorder.edit');

        // Storingen
        Route::get('/storingen', Malfunctions::class)->name('malfunctions');

        // Nieuwe afspraak (alleen manager)
        Route::get('/afspraak/nieuw', CreateAppointment::class)
            ->middleware('can:maintenance-manager')
            ->name('create');
    });
