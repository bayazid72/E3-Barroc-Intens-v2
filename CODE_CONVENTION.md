# Code Conventions - Barroc Intens

> **Laatste update:** 16 november 2025  
> **Project:** E3 Barroc Intens - Bedrijfsapplicatie  
> **Framework:** Laravel 12 met Livewire (Volt) en Flux UI

## Inhoudsopgave

1. [Project Overzicht](#project-overzicht)
2. [PHP Code Conventions](#php-code-conventions)
3. [Laravel Specifieke Conventies](#laravel-specifieke-conventies)
4. [Blade Template Conventions](#blade-template-conventions)
5. [Livewire & Volt Conventions](#livewire--volt-conventions)
6. [JavaScript Conventions](#javascript-conventions)
7. [CSS & Tailwind Conventions](#css--tailwind-conventions)
8. [Database Conventions](#database-conventions)
9. [Testing Conventions](#testing-conventions)
10. [Security & Best Practices](#security--best-practices)
11. [Git & Branching Strategy](#git--branching-strategy)
12. [Naamgeving Conventies](#naamgeving-conventies)

---

## Project Overzicht

### Tech Stack
- **Backend:** Laravel 12.32.5
- **Frontend:** Livewire 3.x met Volt API
- **UI Framework:** Flux UI Components
- **CSS:** Tailwind CSS 4.x
- **Database:** MySQL/MariaDB (via Herd)
- **Testing:** Pest PHP
- **Build Tool:** Vite 7.2.2
- **Development Environment:** Laravel Herd

### Project Structuur
```
e3-barroc-intens/
├── app/
│   ├── Http/Controllers/       # Controllers (minimaal gebruikt door Volt)
│   ├── Livewire/Actions/       # Livewire acties (zoals Logout)
│   ├── Models/                 # Eloquent models
│   └── Providers/              # Service providers
├── database/
│   ├── factories/              # Model factories
│   ├── migrations/             # Database migraties
│   └── seeders/                # Database seeders
├── resources/
│   ├── css/                    # CSS bestanden (Tailwind)
│   ├── js/                     # JavaScript bestanden
│   └── views/                  # Blade templates
│       ├── components/         # Herbruikbare componenten
│       ├── dashboards/         # Dashboard views per rol
│       ├── flux/               # Flux UI overrides
│       └── livewire/           # Livewire Volt componenten
├── routes/
│   ├── web.php                 # Web routes
│   ├── auth.php                # Authenticatie routes
│   └── console.php             # Console routes
└── tests/
    ├── Feature/                # Feature tests
    └── Unit/                   # Unit tests
```

---

## PHP Code Conventions

### 1. PSR-12 Coding Standard

**GEBRUIK ALTIJD PSR-12 als basis voor PHP code.**

**Basisregels:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * User model voor authenticatie en autorisatie
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 */
class User extends Model
{
    // Constanten bovenaan
    public const ROLE_SALES = 'sales';
    public const ROLE_MANAGEMENT = 'management';
    
    // Properties daarna
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    // Methods onderaan
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
```

### 2. Type Declarations

**ALTIJD type hints gebruiken voor parameters en return types.**

**GOED:**
```php
public function createUser(string $name, string $email): User
{
    return User::create([
        'name' => $name,
        'email' => $email,
    ]);
}

protected function validateCredentials(array $credentials): ?User
{
    return User::where('email', $credentials['email'])->first();
}
```

**FOUT:**
```php
public function createUser($name, $email)  // Geen types!
{
    return User::create([
        'name' => $name,
        'email' => $email,
    ]);
}
```

### 3. Docblocks

**Gebruik docblocks voor complexe methods en classes.**

```php
/**
 * Handle an incoming authentication request.
 * 
 * Validates credentials, checks rate limiting, and handles 2FA.
 * 
 * @throws ValidationException
 * @return void
 */
public function login(): void
{
    $this->validate();
    $this->ensureIsNotRateLimited();
    // ...
}
```

### 4. Method Ordering

**Volgorde van methods in een class:**

1. Constructor (`__construct`)
2. Static factory methods
3. Public methods (alfabetisch)
4. Protected methods (alfabetisch)
5. Private methods (alfabetisch)
6. Magic methods (`__toString`, `__get`, etc.)

### 5. Eloquent Model Conventions

**Volgorde binnen een Model:**

```php
class User extends Model
{
    // 1. Traits
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    
    // 2. Constants
    public const ROLE_SALES = 'sales';
    
    // 3. Properties
    protected $table = 'users';
    protected $fillable = ['name', 'email'];
    protected $hidden = ['password'];
    protected $appends = ['initials'];
    
    // 4. Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    // 5. Accessors & Mutators
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // 6. Query Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    // 7. Custom Methods
    public function initials(): string
    {
        // ...
    }
}
```

---

## Laravel Specifieke Conventies

### 1. Service Providers

**Registreer services in de `register()` method, boot ze in `boot()`.**

```php
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
    }
}
```

### 2. Route Definitions

**Gebruik named routes en middleware groups.**

```php
// web.php
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    Route::prefix('settings')->group(function () {
        Volt::route('profile', 'settings.profile')->name('profile.edit');
        Volt::route('password', 'settings.password')->name('password.edit');
    });
});
```

### 3. Migrations

**Gebruik duidelijke namen en volg chronologische volgorde.**

```php
// 2025_11_10_080903_add_role_and_department_to_users_table.php
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('email');
            $table->string('department')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department']);
        });
    }
};
```

### 4. Configuration

**Gebruik altijd `env()` in config bestanden, nooit in applicatie code.**

**GOED (in config/app.php):**
```php
'name' => env('APP_NAME', 'Laravel'),
```

**GOED (in applicatie):**
```php
$appName = config('app.name');
```

**FOUT (in applicatie):**
```php
$appName = env('APP_NAME'); // NOOIT env() in applicatie code!
```

---

## Blade Template Conventions

### 1. Blade Syntax

**Gebruik de juiste Blade directives voor elke situatie.**

```blade
{{-- Commentaar in Blade templates --}}

{{-- Escaped output (standaard) --}}
<h1>{{ $title }}</h1>

{{-- Unescaped output (alleen als nodig) --}}
<div>{!! $htmlContent !!}</div>

{{-- Conditionals --}}
@if ($user->isAdmin())
    <p>Admin dashboard</p>
@elseif ($user->isSales())
    <p>Sales dashboard</p>
@else
    <p>Regular dashboard</p>
@endif

{{-- Loops --}}
@foreach ($users as $user)
    <li>{{ $user->name }}</li>
@endforeach

@forelse ($orders as $order)
    <li>Order #{{ $order->id }}</li>
@empty
    <p>Geen orders gevonden</p>
@endforelse

{{-- Auth checks --}}
@auth
    <p>Welkom terug!</p>
@endauth

@guest
    <a href="{{ route('login') }}">Inloggen</a>
@endguest
```

### 2. Component Usage

**Gebruik Blade components voor herbruikbare UI elementen.**

```blade
{{-- Component met props --}}
<x-auth-header 
    title="Barroc Intens" 
    description="Login met uw account" 
/>

{{-- Flux UI componenten --}}
<flux:heading size="xl">{{ $title }}</flux:heading>
<flux:subheading>{{ $description }}</flux:subheading>

<flux:button variant="primary" type="submit">
    Opslaan
</flux:button>
```

### 3. Layouts

**Gebruik layouts voor consistente pagina structuur.**

```blade
{{-- resources/views/components/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    @include('partials.head')
</head>
<body>
    <flux:header>
        {{-- Navigation --}}
    </flux:header>
    
    <flux:main>
        {{ $slot }}
    </flux:main>
    
    @fluxScripts
</body>
</html>
```

### 4. Props & Attributes

**Gebruik @props directive voor component properties.**

```blade
{{-- Component definitie --}}
@props([
    'title',
    'description',
    'variant' => 'default',
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    <h2>{{ $title }}</h2>
    <p>{{ $description }}</p>
</div>
```

---

## Livewire & Volt Conventions

### 1. Volt Component Structure

**Gebruik Livewire Volt voor single-file componenten.**

```blade
<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();
        
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <form wire:submit="login">
        {{-- Form fields --}}
    </form>
</div>
```

### 2. Wire Directives

**Gebruik wire directives voor interactiviteit.**

```blade
{{-- Model binding --}}
<input wire:model="email" type="email" />
<input wire:model.live="search" type="text" />
<input wire:model.blur="name" type="text" />

{{-- Event handling --}}
<button wire:click="save">Opslaan</button>
<button wire:click="delete({{ $id }})">Verwijderen</button>

{{-- Form submission --}}
<form wire:submit="login">
    {{-- ... --}}
</form>

{{-- Loading states --}}
<div wire:loading>
    Bezig met laden...
</div>

<button wire:loading.attr="disabled">
    Opslaan
</button>

{{-- Targeting specific actions --}}
<div wire:loading wire:target="save">
    Opslaan...
</div>
```

### 3. Livewire Actions

**Gebruik aparte Action classes voor herbruikbare logica.**

```php
// app/Livewire/Actions/Logout.php
namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
```

### 4. Property Validation

**Gebruik Livewire Attributes voor validatie.**

```php
use Livewire\Attributes\Validate;

#[Validate('required|string|min:3')]
public string $name = '';

#[Validate('required|email|unique:users,email')]
public string $email = '';

#[Validate(['name' => 'required|min:3', 'email' => 'required|email'])]
public array $user = [];
```

---

## JavaScript Conventions

### 1. Modern JavaScript (ES6+)

**Gebruik moderne JavaScript syntax.**

```javascript
// Arrow functions
const greet = (name) => `Hello, ${name}!`;

// Destructuring
const { email, password } = form;

// Template literals
const message = `Welcome back, ${user.name}!`;

// Async/await
const fetchData = async () => {
    const response = await fetch('/api/data');
    const data = await response.json();
    return data;
};

// Spread operator
const newUser = { ...user, role: 'admin' };
```

### 2. Alpine.js (indien gebruikt)

**Alpine.js voor kleine interacties.**

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">
        Toggle
    </button>
    
    <div x-show="open" x-transition>
        Content
    </div>
</div>
```

---

## CSS & Tailwind Conventions

### 1. Tailwind Utility Classes

**Gebruik Tailwind utility classes in plaats van custom CSS.**

**GOED:**
```blade
<div class="flex items-center justify-center min-h-screen bg-white">
    <div class="w-full max-w-md p-6 space-y-4">
        <h1 class="text-3xl font-bold text-gray-900">Barroc Intens</h1>
        
        <button class="w-full px-4 py-3 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-lg transition">
            Inloggen
        </button>
    </div>
</div>
```

### 2. Class Ordering

**Volgorde van Tailwind classes:**

1. **Layout:** `flex`, `grid`, `block`, `inline`, `hidden`
2. **Positioning:** `relative`, `absolute`, `fixed`, `top`, `left`
3. **Box Model:** `w-`, `h-`, `p-`, `m-`, `border-`
4. **Typography:** `text-`, `font-`, `leading-`
5. **Visual:** `bg-`, `shadow-`, `rounded-`
6. **Interactive:** `hover:`, `focus:`, `active:`
7. **Responsive:** `sm:`, `md:`, `lg:`, `xl:`

```blade
<button 
    class="
        flex items-center justify-center
        w-full px-4 py-3
        text-sm font-semibold
        bg-yellow-400 hover:bg-yellow-500
        rounded-lg shadow-sm
        transition duration-200
        md:w-auto md:px-6
    "
>
    Opslaan
</button>
```

### 3. Custom Colors

**Gebruik custom colors in theme configuratie.**

```css
/* resources/css/app.css */
@theme {
    --color-brand-yellow: #fbbf24;
    --color-brand-blue: #3b82f6;
    
    --color-zinc-50: #fafafa;
    --color-zinc-900: #171717;
    
    --color-accent: var(--color-neutral-800);
}
```

### 4. Responsive Design

**Mobile-first approach met responsive modifiers.**

```blade
<div class="
    grid grid-cols-1 gap-3
    sm:grid-cols-2
    md:grid-cols-3
    lg:grid-cols-4
">
    {{-- Content --}}
</div>

<button class="
    w-full py-2 text-sm
    md:w-auto md:px-6 md:text-base
">
    Button
</button>
```

### 5. Dark Mode

**Dark mode met custom variant.**

```blade
<div class="bg-white dark:bg-gray-900">
    <h1 class="text-gray-900 dark:text-white">
        Title
    </h1>
</div>
```

```css
@custom-variant dark (&:where(.dark, .dark *));

.dark {
    --color-accent: var(--color-white);
}
```

---

## Database Conventions

### 1. Table Naming

**Gebruik snake_case en meervoud voor tabellen.**

**GOED:**
- `users`
- `orders`
- `customer_orders`
- `product_categories`

**FOUT:**
- `User`
- `order`
- `CustomerOrder`

### 2. Column Naming

**Gebruik snake_case voor kolommen.**

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('role')->nullable();
    $table->string('department')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

### 3. Foreign Keys

**Gebruik conventionele foreign key naming: `{singular_table}_id`.**

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained();
    $table->timestamps();
});
```

### 4. Seeders

**Gebruik duidelijke comments en hashed passwords.**

```php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Testgebruikers voor verschillende afdelingen
        // Wachtwoord voor alle testgebruikers: Password123!
        
        User::factory()->create([
            'name' => 'Sales Medewerker',
            'email' => 'sales@barroc.nl',
            'password' => Hash::make('Password123!'),
            'role' => 'sales',
            'department' => 'Sales',
        ]);
        
        // Meer seeders...
    }
}
```

---

## Testing Conventions

### 1. Pest PHP Test Structure

**Gebruik Pest voor moderne test syntax.**

```php
<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});

it('validates email is required', function () {
    $response = $this->post(route('login'), [
        'password' => 'password',
    ]);
    
    $response->assertSessionHasErrors('email');
});
```

### 2. Test Naming

**Gebruik beschrijvende test namen in natuurlijke taal.**

 **GOED:**
```php
test('user can update their profile')
test('admin can delete users')
test('sales employee can view customer list')
it('requires authentication to access dashboard')
```

 **FOUT:**
```php
test('test1')
test('profile_update')
it('works')
```

### 3. Factory Usage

**Gebruik factories voor test data.**

```php
// In tests
$user = User::factory()->create([
    'role' => 'admin',
]);

$users = User::factory()->count(10)->create();

// Factory definitie
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Password123!'),
            'role' => 'sales',
            'department' => 'Sales',
        ];
    }
}
```

### 4. Test Data Attributes

**Gebruik data-test attributes voor frontend testing.**

```blade
<button 
    type="submit" 
    data-test="login-button"
    class="..."
>
    Inloggen
</button>

<input 
    wire:model="email" 
    data-test="email-input"
    type="email"
/>
```

---

## Security & Best Practices

### 1. Authentication

**Gebruik Laravel Fortify voor authenticatie.**

```php
// Altijd credentials valideren
protected function validateCredentials(): User
{
    $user = Auth::getProvider()->retrieveByCredentials([
        'email' => $this->email,
        'password' => $this->password
    ]);

    if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
        RateLimiter::hit($this->throttleKey());
        
        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    return $user;
}
```

### 2. Rate Limiting

**Implementeer rate limiting voor gevoelige acties.**

```php
protected function ensureIsNotRateLimited(): void
{
    if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
        return;
    }

    event(new Lockout(request()));

    $seconds = RateLimiter::availableIn($this->throttleKey());

    throw ValidationException::withMessages([
        'email' => __('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]),
    ]);
}
```

### 3. Password Validation

**Gebruik sterke password validatie.**

```php
// In AppServiceProvider
Password::defaults(function () {
    return Password::min(8)
        ->letters()
        ->mixedCase()
        ->numbers()
        ->symbols()
        ->uncompromised();
});
```

### 4. Mass Assignment Protection

**Gebruik altijd $fillable of $guarded.**

```php
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
```

### 5. CSRF Protection

**Gebruik CSRF tokens in forms.**

```blade
<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PUT')
    
    {{-- Form fields --}}
</form>

{{-- Met Livewire/Volt is CSRF automatisch --}}
<form wire:submit="save">
    {{-- CSRF is automatic --}}
</form>
```

### 6. XSS Protection

**Escape output, behalve wanneer expliciet niet nodig.**

```blade
{{-- Escaped (default) --}}
{{ $userInput }}

{{-- Unescaped (alleen voor vertrouwde content) --}}
{!! $trustedHtml !!}

{{-- Livewire escapet automatisch --}}
<div>{{ $this->userInput }}</div>
```

### 7. SQL Injection Prevention

**Gebruik Query Builder of Eloquent.**

 **GOED:**
```php
// Eloquent
$user = User::where('email', $email)->first();

// Query Builder
$users = DB::table('users')
    ->where('role', $role)
    ->get();
```

 **FOUT:**
```php
// RAW query zonder binding
DB::select("SELECT * FROM users WHERE email = '$email'");
```

---

## Git & Branching Strategy

### 1. Branch Naming

**Gebruik descriptieve branch namen met prefix.**

```
feature/user-authentication
feature/dashboard-sales
bugfix/login-validation
hotfix/security-patch
refactor/cleanup-controllers
```

### 2. Commit Messages

**Gebruik duidelijke commit messages in Nederlands (project taal).**

```
GOED:
- feat: Voeg login functionaliteit toe
- fix: Los validatie bug op in wachtwoord veld
- refactor: Herstructureer User model
- style: Pas Tailwind classes aan voor login pagina
- test: Voeg tests toe voor dashboard
- docs: Update README met installatie instructies

FOUT:
- update
- fix bug
- changes
- test
```

### 3. Commit Frequency

- Commit vaak en logisch
- Een commit = één logische wijziging
- Niet alle wijzigingen in één grote commit

### 4. .gitignore

**Standaard Laravel .gitignore structuur.**

```gitignore
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
npm-debug.log
yarn-error.log
```

---

## Naamgeving Conventies

### 1. PHP Classes

```php
// PascalCase voor classes
class UserController
class PaymentService
class OrderRepository

// Interface namen eindigen op Interface
interface PaymentGatewayInterface

// Trait namen beschrijven gedrag
trait HasRoles
trait Searchable

// Abstract classes voorvoegsel Abstract
abstract class AbstractController
```

### 2. Methods & Functions

```php
// camelCase voor methods
public function getUserOrders()
public function calculateTotal()
protected function validateInput()

// Boolean methods beginnen met is/has/can
public function isAdmin(): bool
public function hasPermission(string $permission): bool
public function canEdit(): bool
```

### 3. Variables

```php
// camelCase voor variables
$userName = 'John';
$orderTotal = 100.50;
$isActive = true;

// Descriptieve namen
$userEmail (GOED)
$ue (FOUT)

$productPrice (GOED)
$pp (FOUT)

// Collections zijn meervoud
$users = User::all();
$orders = $user->orders;
```

### 4. Constants

```php
// SCREAMING_SNAKE_CASE voor constants
public const MAX_LOGIN_ATTEMPTS = 5;
public const ROLE_ADMIN = 'admin';
public const STATUS_ACTIVE = 'active';
```

### 5. Blade Views

```
// kebab-case voor view bestanden
login.blade.php
two-factor.blade.php
password-reset.blade.php

// Directories ook kebab-case
components/auth-header.blade.php
livewire/settings/two-factor.blade.php
dashboards/sales.blade.php
```

### 6. Routes

```php
// kebab-case voor URL's
Route::get('/profile-settings', ...);
Route::get('/two-factor-authentication', ...);

// Named routes gebruiken dot notation
->name('profile.edit')
->name('two-factor.show')
->name('password.update')
```

### 7. Database

```php
// snake_case voor alles in database
'user_id'
'created_at'
'email_verified_at'
'two_factor_secret'

// Tables zijn meervoud
'users'
'orders'
'product_categories'
```

---

## Belangrijke Project Specifieke Regels

### 1. Authenticatie

- **2FA is momenteel UITGESCHAKELD** maar code blijft aanwezig voor toekomstige activatie
- Wachtwoorden moeten voldoen aan: min 8 karakters, hoofdletters, kleine letters, cijfers, symbolen
- Rate limiting: max 5 inlogpogingen
- Demo accounts allemaal wachtwoord: `Password123!`

### 2. Rollen & Departments

Beschikbare rollen:
- `sales` - Sales afdeling
- `inkoop` - Inkoop afdeling
- `finance` - Financiële afdeling
- `onderhoud` - Onderhoud afdeling
- `planning` - Planning afdeling
- `management` - Management

### 3. UI/UX Richtlijnen

- **Primary color:** Yellow (#fbbf24 / yellow-400)
- **Dark mode:** Momenteel niet actief op login pagina
- **Responsive:** Mobile-first design
- **Icons:** Lock icon met gele achtergrond voor beveiligde secties
- **Buttons:** Gele achtergrond voor primary actions

### 4. Development Workflow

```bash
# Start development server
npm run dev

# Run tests
php artisan test

# Run specific test
php artisan test --filter DashboardTest

# Database migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Code Review Checklist

Voordat je code commit, check het volgende:

### PHP Code
- [ ] PSR-12 compliant
- [ ] Type hints gebruikt
- [ ] Docblocks toegevoegd waar nodig
- [ ] Geen hardcoded values
- [ ] Error handling aanwezig
- [ ] Security best practices gevolgd

### Blade Templates
- [ ] Proper escaping gebruikt
- [ ] Components herbruikbaar gemaakt
- [ ] Tailwind classes gebruikt (geen inline styles)
- [ ] Responsive design geïmplementeerd
- [ ] Accessibility attributen toegevoegd

### Livewire/Volt
- [ ] Validatie regels correct
- [ ] Wire directives efficiënt gebruikt
- [ ] Loading states toegevoegd
- [ ] Error handling geïmplementeerd

### Testing
- [ ] Feature tests geschreven
- [ ] Test namen beschrijvend
- [ ] Edge cases getest
- [ ] Factories gebruikt

### Database
- [ ] Migrations rollback-able
- [ ] Foreign keys correct ingesteld
- [ ] Indexes toegevoegd waar nodig
- [ ] Seeders up-to-date

### Git
- [ ] Branch naam correct
- [ ] Commit message duidelijk
- [ ] Geen debug code
- [ ] .env.example up-to-date

---

## Referenties & Resources

### Documentatie
- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Flux UI Documentation](https://fluxui.dev/docs)
- [Pest PHP Documentation](https://pestphp.com/docs)

### Style Guides
- [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)

### Tools
- [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
- [PHPStan](https://phpstan.org/)
- [Laravel Pint](https://laravel.com/docs/pint)

---

## Contact & Support

Voor vragen over deze conventions:
- Zie de project README.md
- Check de Laravel documentatie
- Vraag een teamlid

**Laatste update:** 16 november 2025  
**Versie:** 1.0.0
