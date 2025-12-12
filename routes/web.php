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
use App\Livewire\Purchase\PaidInvoicesManager;
use App\Livewire\Purchase\ProductOverview;


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
use App\Livewire\Purchase\InvoiceTasks;
use Illuminate\Http\Request;
use App\Models\Invoice;


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

Route::get('/dashboard', function () {
    $role = auth()->user()->role?->name;

    return match ($role) {
        'Manager'              => redirect()->route('users.index'),          
        'Inkoop'               => redirect()->route('purchase.dashboard'),
        'Finance'              => redirect()->route('finance.contracts'),
        'MaintenanceManager'   => redirect()->route('maintenance.dashboard.manager'),
        'Maintenance'          => redirect()->route('maintenance.dashboard'),
        default                => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');


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
        $user = auth()->user();

        // Alleen Admin / Manager mogen deze pagina zien
        if (! $user || ! in_array($user->role?->name, ['Admin', 'Manager'])) {
            abort(403);
        }

        // 👇 HIER bouw je de query op
        $query = LoginAttempt::with('user')->latest();

        // Zoekterm (email of naam)
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status-filter: successful / failed / blocked
        if ($status = request('status')) {
            if ($status === 'successful') {
                $query->where('successful', true);
            } elseif ($status === 'failed') {
                $query->where('successful', false)->where('blocked', false);
            } elseif ($status === 'blocked') {
                $query->where('blocked', true);
            }
        }

        // Uiteindelijk de query uitvoeren + filters in de paginatie URL houden
        $logs = $query->paginate(50)->withQueryString();

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

        // Dashboard & beheer
        Route::get('/', PurchaseDashboard::class)->name('dashboard');
        Route::get('/products', ProductManager::class)->name('products');
        Route::get('/stock', StockManager::class)->name('stock');
        Route::get('/suppliers', SupplierManager::class)->name('suppliers');

        // Overzichten
        Route::get('/products/overview', ProductOverview::class)
            ->name('products.overview');

        // Paid invoices
        Route::get('/paid-invoices', PaidInvoicesManager::class)
            ->name('paid-invoices');
    });
Route::middleware(['auth'])
    ->get('/products', ProductOverview::class)
    ->name('products.overview');

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
        Route::get('/facturen', \App\Livewire\Finance\InvoiceList::class)->name('invoices');
        Route::get('/facturen/nieuw', \App\Livewire\Finance\InvoiceCreate::class)->name('invoices.create');
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

        // Nieuwe afspraak
        Route::get('/afspraak/nieuw', CreateAppointment::class)
            ->middleware('can:maintenance-manager')
            ->name('create');

        // Afspraak details + edit
        Route::get('/view/{appointment}', \App\Livewire\Maintenance\ViewAppointment::class)
            ->name('view');

        Route::get('/afspraak/{appointment}/edit', \App\Livewire\Maintenance\EditAppointment::class)
            ->name('edit');

        // Monteur dag/week
        Route::get('/mijn-bezoeken/dag', \App\Livewire\Maintenance\TechnicianDayView::class)
            ->name('visits.day');

        Route::get('/mijn-bezoeken/week', \App\Livewire\Maintenance\TechnicianWeekView::class)
            ->name('visits.week');

        // 🔔 NOTIFICATIES (dit moest binnen deze groep!)
        Route::get('/notificaties', \App\Livewire\Maintenance\Notifications::class)
            ->middleware('can:maintenance-manager')
            ->name('notifications');
});
//forgot-password
Volt::route('/forgot-password', 'auth.forgot-password')->name('password.request');
Volt::route('/reset-password/{token}', 'auth.reset-password')->name('password.reset');



