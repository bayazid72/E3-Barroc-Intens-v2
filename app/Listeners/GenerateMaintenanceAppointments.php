<?php

namespace App\Listeners;

use App\Events\ContractApproved;
use App\Models\Appointment;
use Carbon\Carbon;

class GenerateMaintenanceAppointments
{
    /**
     * Handle the event.
     */
    public function handle(ContractApproved $event): void
    {
        $contract = $event->contract;

        // Bereken het aantal maanden tussen start en eind datum
        $startDate = Carbon::parse($contract->starts_at);
        $endDate = $contract->ends_at 
            ? Carbon::parse($contract->ends_at) 
            : $startDate->copy()->addMonths(12)->subDay(); // 12 maanden vanaf start

        $intervalMonths = $contract->periodic_interval_months ?? 1; // Standaard maandelijks

        // Genereer appointments voor elke periode
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            Appointment::create([
                'company_id' => $contract->company_id,
                'contract_id' => $contract->id,
                'technician_id' => null, // Kan later toegewezen worden
                'type' => 'routine',
                'description' => "Automatisch gegenereerd periodiek onderhoud voor contract #{$contract->id}",
                'date_planned' => $currentDate->copy(),
                'date_added' => now(),
                'status' => 'open',
            ]);

            // Ga naar de volgende periode
            $currentDate->addMonths($intervalMonths);
        }
    }
}
