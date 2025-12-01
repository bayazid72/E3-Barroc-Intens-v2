# Offerte naar Factuur Conversie - Documentatie

## Overzicht
Deze feature implementeert de mogelijkheid om offertes automatisch om te zetten naar facturen met behoud van alle gegevens.

## Afdeling
Financiën

## Functionaliteit

### Wat doet het?
- Converteert een bestaande offerte met één klik naar een factuur
- Neemt alle gegevens over:
  - Producten en aantallen
  - Prijzen per product (price_snapshot)
  - Klantgegevens
  - Contractinformatie
  - Totaalbedrag

### Voordelen
- **Tijdsbesparing**: Geen dubbele invoer meer nodig
- **Foutpreventie**: Geen typfouten of vergeten gegevens
- **Traceerbaar**: Offertes worden gemarkeerd als "omgezet"
- **Automatisch factuurnummer**: Wordt automatisch gegenereerd (INV-YYYY-0001 formaat)

## Technische Implementatie

### Models
1. **Quote Model** (`app/Models/Quote.php`)
   - Gebruikt dezelfde `invoices` tabel met `type='quote'`
   - Global scope om alleen offertes op te halen
   - Method: `convertToInvoice()` - Handelt de conversie af
   - Method: `canBeConverted()` - Controleert of conversie mogelijk is

2. **Invoice Model** (`app/Models/Invoice.php`)
   - Global scope om alleen facturen op te halen (`type='invoice'`)
   - Automatische factuurnummer generatie bij aanmaken
   - Format: `INV-{jaar}-{volgnummer}` (bijv. INV-2025-0001)

### Livewire Components

1. **ConvertQuoteToInvoice** (`app/Livewire/Finance/ConvertQuoteToInvoice.php`)
   - Toont "Maak Factuur" knop
   - Bevestigingsmodal met samenvatting
   - Handelt conversieproces af
   - Geeft visuele feedback

2. **QuoteList** (`app/Livewire/Finance/QuoteList.php`)
   - Overzicht van alle offertes
   - Zoeken en filteren op status
   - Directe toegang tot conversieknop

3. **QuoteShow** (`app/Livewire/Finance/QuoteShow.php`)
   - Detailweergave van offerte
   - Toon alle producten en prijzen
   - Conversieknop in header

### Routes
```php
// routes/web.php
Route::middleware(['auth', 'can:access-finance'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {
        Route::get('/offertes', QuoteList::class)->name('quotes.index');
        Route::get('/offertes/{quote}', QuoteShow::class)->name('quotes.show');
        Route::get('/facturen/{invoice}', InvoiceShow::class)->name('invoices.show');
    });
```

### Database
- **Tabel**: `invoices` (gebruikt voor zowel quotes als invoices)
- **Type veld**: `enum('invoice', 'quote')`
- **Nieuw veld**: `invoice_number` (string, nullable)

## Gebruik

### Voor Financiën Medewerkers

1. **Overzicht bekijken**
   - Ga naar `/finance/offertes`
   - Zie alle offertes met status

2. **Offerte omzetten**
   - Klik op "Maak Factuur" bij een offerte
   - Controleer de samenvatting in het modal
   - Bevestig de conversie

3. **Bevestiging**
   - Success melding toont factuurnummer
   - Automatische redirect naar factuur
   - Offerte status wordt "Omgezet"

### Acceptatiecriteria ✅
- [x] Vanuit een offerte is een "Maak factuur" knop beschikbaar
- [x] Resulterende factuur neemt alle velden 1-op-1 over
- [x] Visuele bevestiging dat conversie geslaagd is
- [x] Factuurnummer wordt automatisch gegenereerd

## Tests

Alle functionaliteit is getest met unit tests:

```bash
php artisan test --filter=QuoteToInvoiceConversion
```

### Test Cases
1. ✅ Quote kan worden omgezet naar factuur
2. ✅ Quote zonder regels kan niet worden omgezet
3. ✅ Al omgezette quote kan niet opnieuw worden omgezet
4. ✅ Factuurnummer wordt automatisch gegenereerd
5. ✅ Factuurnummer increments correct

## Uitbreidingsmogelijkheden

Deze implementatie is zo opgezet dat er makkelijk uitbreidingen mogelijk zijn:

1. **PDF Generatie**: Factuur direct downloaden als PDF
2. **Email Verzending**: Factuur automatisch mailen naar klant
3. **Notificaties**: Team notificeren bij nieuwe factuur
4. **Batch Conversie**: Meerdere offertes tegelijk omzetten
5. **Goedkeuringsflow**: Conversie vereist goedkeuring manager
6. **Historie**: Log bijhouden van conversies
7. **Terugdraaien**: Mogelijkheid om conversie ongedaan te maken

## Code Kwaliteit

- **Transaction Safety**: Conversie gebeurt in database transaction
- **Error Handling**: Try-catch met gebruiksvriendelijke foutmeldingen
- **Validation**: Controles of conversie mogelijk is
- **Events**: Dispatcht events voor andere components
- **Type Safety**: Strikte type hints in PHP code
- **Tests**: Volledige test coverage

## Onderhoud

### Seeder Data
Test data genereren:
```bash
php artisan db:seed --class=QuoteSeeder
```

### Migratie
Nieuwe installatie:
```bash
php artisan migrate
```

## Contact
Voor vragen over deze functionaliteit, neem contact op met het ontwikkelteam.
