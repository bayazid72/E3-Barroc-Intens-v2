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
use App\Livewire\Maintenance\ViewAppointment;

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
        $user = Auth::user();

        // Alleen rollen Admin / Management
        if (! $user || ! in_array($user->role?->name, ['Admin', 'Manager'])) {
            abort(403);
        }

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
    });

Route::middleware(['auth'])
    ->middleware('can:access-finance')
    ->group(function () {

        Route::get('/finance/facturen', InvoiceManager::class)->name('finance.invoices');
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
          //view van een appointment
        Route::get('/view/{appointment}', \App\Livewire\Maintenance\ViewAppointment::class)
             ->name('maintenance.view');
             // Afspraak bewerken
         Route::get('/afspraak/{appointment}/edit', \App\Livewire\Maintenance\EditAppointment::class)
            ->name('edit');


    });
