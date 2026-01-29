<?php

use App\Models\Appointment;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it generates monthly appointments when contract is approved', function () {
    // Arrange: Maak een bedrijf en een contract
    $company = Company::create([
        'name' => 'Test Company',
        'street' => 'Test Street',
        'house_number' => '1',
        'city' => 'Test City',
        'zip' => '1234AB',
        'phone' => '0612345678',
        'country_code' => 'NL',
        'bkr_checked' => false,
    ]);
    
    $role = Role::create(['name' => 'Admin']);
    
    $user = User::create([
        'name' => 'Test User',
        'email' => 'user@example.com',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
    ]);

    $contract = Contract::create([
        'company_id' => $company->id,
        'starts_at' => '2026-02-01',
        'ends_at' => '2026-05-01',
        'invoice_type' => 'monthly',
        'periodic_interval_months' => 1, // Maandelijks
        'created_by' => $user->id,
        'status' => 'pending',
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
    $company = Company::create([
        'name' => 'Test Company 2',
        'street' => 'Test Street',
        'house_number' => '2',
        'city' => 'Test City',
        'zip' => '1234AB',
        'phone' => '0612345678',
        'country_code' => 'NL',
        'bkr_checked' => false,
    ]);
    
    $role = Role::firstOrCreate(['name' => 'Admin']);
    
    $user = User::create([
        'name' => 'Test User 2',
        'email' => 'user2@example.com',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
    ]);

    $contract = Contract::create([
        'company_id' => $company->id,
        'starts_at' => '2026-01-01',
        'ends_at' => '2026-12-31',
        'invoice_type' => 'periodic',
        'periodic_interval_months' => 3, // Per kwartaal
        'created_by' => $user->id,
        'status' => 'pending',
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
    $company = Company::create([
        'name' => 'Test Company 3',
        'street' => 'Test Street',
        'house_number' => '3',
        'city' => 'Test City',
        'zip' => '1234AB',
        'phone' => '0612345678',
        'country_code' => 'NL',
        'bkr_checked' => false,
    ]);
    
    $role = Role::firstOrCreate(['name' => 'Admin']);
    
    $user = User::create([
        'name' => 'Test User 3',
        'email' => 'user3@example.com',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
    ]);

    $contract = Contract::create([
        'company_id' => $company->id,
        'starts_at' => '2026-01-01',
        'ends_at' => null, // Geen einddatum
        'invoice_type' => 'monthly',
        'periodic_interval_months' => 1,
        'created_by' => $user->id,
        'status' => 'pending',
    ]);

    // Act: Keur het contract goed
    $contract->approve();

    // Assert: Er zijn 12 maandelijkse appointments voor het eerste jaar
    $appointments = Appointment::where('contract_id', $contract->id)->get();
    expect($appointments)->toHaveCount(12);
});

test('contract approval status methods work', function () {
    // Arrange
    $company = Company::create([
        'name' => 'Test Company 4',
        'street' => 'Test Street',
        'house_number' => '4',
        'city' => 'Test City',
        'zip' => '1234AB',
        'phone' => '0612345678',
        'country_code' => 'NL',
        'bkr_checked' => false,
    ]);
    
    $role = Role::firstOrCreate(['name' => 'Admin']);
    
    $user = User::create([
        'name' => 'Test User 4',
        'email' => 'user4@example.com',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
    ]);

    $contract = Contract::create([
        'company_id' => $company->id,
        'starts_at' => '2026-01-01',
        'ends_at' => '2026-12-31',
        'invoice_type' => 'monthly',
        'periodic_interval_months' => 1,
        'created_by' => $user->id,
        'status' => 'pending',
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

