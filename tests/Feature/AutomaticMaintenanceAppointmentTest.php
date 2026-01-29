<?php

use App\Models\Appointment;
use App\Models\Company;
use App\Models\Contract;
use App\Models\User;

test('it generates monthly appointments when contract is approved', function () {
    // Arrange: Maak een bedrijf en een contract
    $company = Company::factory()->create();
    $user = User::factory()->create();

    $contract = Contract::create([
        'company_id' => $company->id,
        'starts_at' => '2026-02-01',
        'ends_at' => '2026-05-01',
        'invoice_type' => 'monthly',
        'periodic_interval_months' => 1, // Maandelijks
        'created_by' => $user->id,
        'status' => 'draft',
    ]);

    // Assert: Initieel geen appointments
    expect(Appointment::count())->toBe(0);

    // Act: Keur het contract goed
    $contract->approve();

    // Assert: Er zijn nu 4 appointments (Feb, Mrt, Apr, Mei)
    expect(Appointment::count())->toBe(4);

    // Controleer dat alle appointments correct zijn aangemaakt
    $appointments = Appointment::where('contract_id', $contract->id)
        ->orderBy('date_planned')
        ->get();

    expect($appointments)->toHaveCount(4);
    
    // Controleer eerste appointment
    expect($appointments[0]->company_id)->toBe($company->id);
    expect($appointments[0]->contract_id)->toBe($contract->id);
    expect($appointments[0]->type)->toBe('routine');
    expect($appointments[0]->status)->toBe('open');
    expect($appointments[0]->date_planned->format('Y-m-d'))->toBe('2026-02-01');

    // Controleer tweede appointment
    expect($appointments[1]->date_planned->format('Y-m-d'))->toBe('2026-03-01');
    
    // Controleer derde appointment
    expect($appointments[2]->date_planned->format('Y-m-d'))->toBe('2026-04-01');
    
    // Controleer vierde appointment
    expect($appointments[3]->date_planned->format('Y-m-d'))->toBe('2026-05-01');
});

test('it generates quarterly appointments with custom interval', function () {
    // Arrange: Maak een contract met driemaandelijks interval
    $company = Company::factory()->create();
    $user = User::factory()->create();

    $contract = Contract::create([
        'company_id' => $company->id,
        'starts_at' => '2026-01-01',
        'ends_at' => '2026-12-31',
        'invoice_type' => 'periodic',
        'periodic_interval_months' => 3, // Per kwartaal
        'created_by' => $user->id,
        'status' => 'draft',
    ]);

    // Act: Keur het contract goed
    $contract->approve();

    // Assert: Er zijn 4 kwartaal appointments (Q1, Q2, Q3, Q4)
    $appointments = Appointment::where('contract_id', $contract->id)
        ->orderBy('date_planned')
        ->get();

    expect($appointments)->toHaveCount(4);
    expect($appointments[0]->date_planned->format('Y-m-d'))->toBe('2026-01-01');
    expect($appointments[1]->date_planned->format('Y-m-d'))->toBe('2026-04-01');
    expect($appointments[2]->date_planned->format('Y-m-d'))->toBe('2026-07-01');
    expect($appointments[3]->date_planned->format('Y-m-d'))->toBe('2026-10-01');
});

test('it generates appointments for one year when no end date', function () {
    // Arrange: Maak een contract zonder einddatum
    $company = Company::factory()->create();
    $user = User::factory()->create();

    $contract = Contract::create([
        'company_id' => $company->id,
        'starts_at' => '2026-01-01',
        'ends_at' => null, // Geen einddatum
        'invoice_type' => 'monthly',
        'periodic_interval_months' => 1,
        'created_by' => $user->id,
        'status' => 'draft',
    ]);

    // Act: Keur het contract goed
    $contract->approve();

    // Assert: Er zijn 12 maandelijkse appointments voor het eerste jaar
    $appointments = Appointment::where('contract_id', $contract->id)->get();
    expect($appointments)->toHaveCount(12);
});

test('contract approval status methods work', function () {
    // Arrange
    $company = Company::factory()->create();
    $user = User::factory()->create();

    $contract = Contract::create([
        'company_id' => $company->id,
        'starts_at' => '2026-01-01',
        'ends_at' => '2026-12-31',
        'invoice_type' => 'monthly',
        'periodic_interval_months' => 1,
        'created_by' => $user->id,
        'status' => 'draft',
    ]);

    // Assert: Initial status
    expect($contract->isDraft())->toBeTrue();
    expect($contract->isApproved())->toBeFalse();

    // Act: Approve
    $contract->approve();

    // Assert: Status changed
    $contract->refresh();
    expect($contract->isApproved())->toBeTrue();
    expect($contract->isDraft())->toBeFalse();
});

