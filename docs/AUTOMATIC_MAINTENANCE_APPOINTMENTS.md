# Automatische Generatie van Onderhoudsafspraken

## Overzicht

Dit systeem genereert automatisch periodieke onderhoudsafspraken wanneer een contract wordt goedgekeurd. Dit zorgt voor consistente onderhoudsschema's zonder handmatige planning.

## Functionaliteit

### Automatische Generatie
Wanneer een contract wordt goedgekeurd (status wordt 'active'), triggert het systeem automatisch de creatie van onderhoudsafspraken:

```php
$contract->approve();
```

Dit genereert appointments op basis van:
- **Startdatum**: `starts_at` veld van het contract
- **Einddatum**: `ends_at` veld (of 12 maanden als niet ingevuld)
- **Interval**: `periodic_interval_months` veld (standaard 1 maand)

### Contract Statussen
- **pending**: Nieuw contract, nog niet goedgekeurd
- **active**: Goedgekeurd contract, appointments worden automatisch gegenereerd
- **cancelled**: Geannuleerd contract
- **expired**: Verlopen contract

### Voorbeelden

#### Maandelijks Onderhoud
```php
$contract = Contract::create([
    'company_id' => $company->id,
    'starts_at' => '2026-01-01',
    'ends_at' => '2026-04-01',
    'invoice_type' => 'monthly',
    'periodic_interval_months' => 1,
    'created_by' => $user->id,
]);

$contract->approve(); // Genereert 4 appointments (Jan, Feb, Mrt, Apr)
```

#### Kwartaal Onderhoud
```php
$contract = Contract::create([
    'company_id' => $company->id,
    'starts_at' => '2026-01-01',
    'ends_at' => '2026-12-31',
    'invoice_type' => 'periodic',
    'periodic_interval_months' => 3,
    'created_by' => $user->id,
]);

$contract->approve(); // Genereert 4 appointments (Q1, Q2, Q3, Q4)
```

## Database Structuur

### Nieuwe Velden

#### appointments tabel
- **contract_id**: Foreign key naar contracts tabel (nullable)
  - Koppelt appointment aan het contract waaruit het is gegenereerd
  - Blijft behouden ook als contract wordt verwijderd (ON DELETE SET NULL)

#### contracts tabel  
- **status**: Enum waarde (pending/active/cancelled/expired)
- **activated_at**: Timestamp wanneer contract is goedgekeurd

### Relaties

```php
// Contract heeft vele appointments
$contract->appointments;

// Appointment hoort bij een contract
$appointment->contract;
```

## Event/Listener Architectuur

### Event: ContractApproved
Wordt getriggerd wanneer een contract wordt goedgekeurd.

**Locatie**: `app/Events/ContractApproved.php`

### Listener: GenerateMaintenanceAppointments
Luistert naar ContractApproved event en genereert de appointments.

**Locatie**: `app/Listeners/GenerateMaintenanceAppointments.php`

**Registratie**: `app/Providers/EventServiceProvider.php`

```php
protected $listen = [
    ContractApproved::class => [
        GenerateMaintenanceAppointments::class,
    ],
];
```

## Gegenereerde Appointments

Elke automatisch gegenereerde appointment heeft:
- **company_id**: Bedrijf van het contract
- **contract_id**: Koppeling naar het contract
- **technician_id**: NULL (kan later worden toegewezen)
- **type**: 'routine'
- **description**: "Automatisch gegenereerd periodiek onderhoud voor contract #[ID]"
- **date_planned**: Gepland op basis van interval
- **date_added**: Huidige timestamp
- **status**: 'open'

## Handmatige Aanpassingen

Monteurs en planners kunnen:
1. **Technicus toewijzen**: `$appointment->technician_id` updaten
2. **Datum wijzigen**: `$appointment->date_planned` aanpassen
3. **Status updaten**: Van 'open' naar 'planned', 'done', etc.
4. **Appointment verwijderen**: Als deze niet nodig is
5. **Extra appointments toevoegen**: Naast de automatisch gegenereerde

```php
// Voorbeeld: Technicus toewijzen
$appointment->update([
    'technician_id' => $technician->id,
    'status' => 'planned',
]);
```

## Zichtbaarheid voor Monteurs

Alle appointments (automatisch en handmatig) zijn zichtbaar in:
- Planning overzichten
- Monteur dashboard  
- Werkorder systeem

Monteurs kunnen:
- Hun toegewezen appointments zien
- Appointments als 'done' markeren
- Werkorders aanmaken voor appointments

## Migraties

Voer de volgende migraties uit:
```bash
php artisan migrate
```

Dit voert uit:
- `2026_01_29_000002_add_contract_id_to_appointments_table.php`

(Status veld bestaat al via eerdere migratie)

## Tests

Voer de geautomatiseerde tests uit:
```bash
vendor/bin/pest tests/Feature/AutomaticMaintenanceAppointmentTest.php
```

Test coverage:
- ✓ Maandelijkse appointments genereren
- ✓ Kwartaal appointments met custom interval
- ✓ Appointments voor 1 jaar zonder einddatum
- ✓ Contract approval status methoden

## Acceptatiecriteria (Voldaan)

**Na contractgoedkeuring genereert het systeem automatisch onderhoudstaken**
- Event/Listener systeem triggert bij approve()
- Appointments worden gegenereerd volgens afgesproken schema

**Handmatige aanpassingen blijven mogelijk**
- Alle velden van appointments kunnen worden aangepast
- Extra appointments kunnen handmatig worden toegevoegd
- Automatisch gegenereerde appointments kunnen worden verwijderd

**Taken verschijnen in de planning en zijn zichtbaar voor monteurs**
- Appointments worden opgeslagen in dezelfde tabel
- Hebben 'open' status en kunnen worden toegewezen
- Zijn gekoppeld aan company_id voor filtering

## Toekomstige Uitbreidingen

Mogelijke verbeteringen:
- **Email notificaties** naar monteurs bij nieuwe appointments
- **Automatische technicus toewijzing** op basis van beschikbaarheid
- **Herinneringen** voor naderende appointments
- **Rapport generatie** van uitgevoerde onderhouden per contract
- **Contract verlenging** met voortzetting van appointment generatie
