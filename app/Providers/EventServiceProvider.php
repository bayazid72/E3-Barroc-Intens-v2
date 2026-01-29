<?php

namespace App\Providers;

use App\Events\ContractApproved;
use App\Events\ProductUsed;
use App\Listeners\GenerateMaintenanceAppointments;
use App\Listeners\NotifyPurchasingOfUsage;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event-to-listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Wanneer een gebruiker zich registreert
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // JOUW EVENT - wanneer monteurs materialen gebruiken
        ProductUsed::class => [
            NotifyPurchasingOfUsage::class,
        ],

        // Wanneer een contract wordt goedgekeurd
        ContractApproved::class => [
            GenerateMaintenanceAppointments::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        //
        // Je kunt hier extra events registreren indien nodig.
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
