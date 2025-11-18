<?php

use App\Models\User;
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

        $this->ensureIsNotRateLimited();

        $user = $this->validateCredentials();

        if (Features::canManageTwoFactorAuthentication() && $user->hasEnabledTwoFactorAuthentication()) {
            Session::put([
                'login.id' => $user->getKey(),
                'login.remember' => $this->remember,
            ]);

            $this->redirect(route('two-factor.login'), navigate: true);

            return;
        }

        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Validate the user's credentials.
     */
    protected function validateCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $this->email, 'password' => $this->password]);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
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

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-6">

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
