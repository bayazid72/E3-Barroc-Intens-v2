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
            return $user->role && $user->role->name === 'Manager';
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
            return in_array($user->role->name, ['MaintenanceManager', 'Manager',]);
        });

        // is admin (voor login logs)
        Gate::define('view-login-logs', function (User $user) {
            return (bool) ($user->is_admin ?? false);
        });

        Gate::define('access-maintenance', function ($user) {
    return in_array($user->role->name, [
        'Maintenance',          // ← monteur
        'MaintenanceManager',
        'Manager',
    ]);
});
Gate::define('maintenance-tech', function ($user) {
    return $user->role->name === 'Maintenance';
});









    }

}
