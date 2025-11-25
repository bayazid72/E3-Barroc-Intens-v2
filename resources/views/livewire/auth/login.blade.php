<?php

use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
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

        // Controleer throttle; als false, ensureIsNotRateLimited voegde al een fout toe.
        if (! $this->ensureIsNotRateLimited()) {
            return;
        }

        $user = $this->validateCredentials();

        // validateCredentials voegde al een fout toe als null
        if (! $user) {
            return;
        }

        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // SUCCESVOLLE LOGIN LOGGEN
        LoginAttempt::create([
            'user_id'    => $user->id,
            'email'      => $this->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'successful' => true,
            'blocked'    => false,
        ]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     * Return true = ok to continue, false = stopped (and an error was added).
     */
    protected function ensureIsNotRateLimited(): bool
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return true;
        }

        // Log de geblokkeerde poging
        LoginAttempt::create([
            'user_id'    => null,
            'email'      => $this->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'successful' => false,
            'blocked'    => true,
        ]);

        // Voeg Livewire-compatibele fout toe (i.p.v. throw)
        $seconds = RateLimiter::availableIn($this->throttleKey());
        $this->addError('email', 'Te veel inlogpogingen. Probeer het over '.$seconds.' seconden opnieuw.', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 30),
        ]);

        return false;
    }

    /**
     * Validate the user's credentials.
     * Returns User on success, null on failure (and sets UI error).
     */
    protected function validateCredentials(): ?User
    {
        $credentials = ['email' => $this->email, 'password' => $this->password];

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, $credentials)) {
            // Log de mislukte poging
            LoginAttempt::create([
                'user_id'    => $user?->id,
                'email'      => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'successful' => false,
                'blocked'    => false,
            ]);

            // Rate limiter hitten
            RateLimiter::hit($this->throttleKey());

            // Geef directe Livewire-validatie feedback
            $this->addError('email', 'Onjuiste email of wachtwoord');

            return null;
        }

        return $user;
    }
    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
};
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-6">
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-md text-sm mb-3">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Email -->
    <div class="flex flex-col gap-1">
        <label class="text-gray-700 font-medium">Email</label>
        <input type="email"
               wire:model="email"
               class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-[#FFD600] focus:border-[#FFD600]"
               required />
    </div>

    <!-- Password -->
    <div class="flex flex-col gap-1">
        <label class="text-gray-700 font-medium">Password</label>
        <input type="password"
               wire:model="password"
               class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-[#FFD600] focus:border-[#FFD600]"
               required />
    </div>

    <!-- Remember me -->
    <label class="flex items-center gap-2 text-gray-700">
        <input type="checkbox"
               wire:model="remember"
               class="w-4 h-4 border-gray-300 rounded" />
        Remember me
    </label>

    <!-- Bottom row -->
    <div class="flex items-center justify-between">

        <!-- Forgot password -->
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}"
               class="text-blue-600 text-sm underline">
                Forgot your password?
            </a>
        @endif

        <!-- Login Button -->
        <button type="submit"
                wire:click="login"
                class="bg-[#FFD600] hover:bg-[#e5c000] px-8 py-3 rounded-md font-semibold shadow text-black">
            LOG IN
        </button>

    </div>
        <div class="mt-4">
        <p class="text-center text-sm text-gray-600 mb-3">Demo accounts (gebruik elk wachtwoord):</p>
        <div class="grid grid-cols-2 gap-3">
            <button
                type="button"
                wire:click="$set('email', 'sales@example.com')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
                Sales
            </button>
            <button
                type="button"
                wire:click="$set('email', 'inkoop@example.com')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
                Inkoop
            </button>
            <button
                type="button"
                wire:click="$set('email', 'finance@example.com')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
                Financieel
            </button>
            <button
                type="button"
                wire:click="$set('email', 'maintmanager@example.com')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
                Onderhoud
            </button>
            <button
                type="button"
                wire:click="$set('email', 'planning@barroc.nl')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
                Planning (nog niet werkend)
            </button>
            <button
                type="button"
                wire:click="$set('email', 'admin@example.com')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
                Management
            </button>
        </div>
        <p class="text-center text-xs text-gray-500 mt-3">Deze applicatie is beveiligd volgens AVG-richtlijnen</p>
    </div>
    
</div>

</div>
