<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Manager mag ALLES beheren
        Gate::define('manage-users', function (User $user) {
            return in_array($user->role?->name, [
                'Manager',
                'Sales',
            ]);
        });

        // INKOOP toegang
        Gate::define('purchase-access', function ($user) {
            return in_array($user->role->name, ['Inkoop', 'Manager']);
        });

        // FINANCE toegang
        Gate::define('access-finance', function ($user) {
            return in_array($user->role->name, ['Finance', 'Manager']);
        });


        // MAINTENANCE MANAGER (alleen hoofd maintenance + manager)
        Gate::define('maintenance-manager', function ($user) {
            return in_array($user->role->name, ['MaintenanceManager', 'Manager', 'Sales', ]);
        });

        // is admin (voor login logs)
        Gate::define('view-login-logs', function (User $user) {
            return (bool) ($user->is_admin ?? false);
        });

        // SALES toegang
        Gate::define('sales-access', function ($user) {
            return in_array($user->role->name, [
                'Sales',
                'Manager',
            ]);
        });
        Gate::define('maintenance-appointments', function ($user) {
            return in_array($user->role->name, [
                'Sales',
                'MaintenanceManager',
                'Manager',
                'Maintenance',
            ]);
        });

            Gate::define('access-maintenance', function ($user) {
            return in_array($user->role->name, [
            'Maintenance',          // ← monteur
            'MaintenanceManager',
            'Manager',
            'Sales',
            ]);
        });
        Gate::define('maintenance-tech', function ($user) {
            return $user->role->name === 'Maintenance';
        });
    }

}
