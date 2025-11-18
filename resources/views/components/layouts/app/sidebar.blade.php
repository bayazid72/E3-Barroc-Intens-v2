<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">

<flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
        <x-app-logo />
    </a>

    <flux:navlist variant="outline">

        {{-- DASHBOARD --}}
        <flux:navlist.group :heading="__('')" class="grid">
            <flux:navlist.item
                icon="home"
                :href="route('dashboard')"
                :current="request()->routeIs('dashboard')"
                wire:navigate>
                Dashboard
            </flux:navlist.item>
        </flux:navlist.group>


        {{-- 1. GEBRUIKERSBEHEER --}}
        @can('manage-users')
            <a href="{{ route('users.index') }}"
               class="flex items-center gap-3 p-3 rounded-lg border border-neutral-300 hover:border-yellow-500 transition mb-3">
                <div class="font-semibold text-lg">👤 Gebruikersbeheer</div>
            </a>
        @endcan


        {{-- 2. OFFERTES
        @if(in_array(auth()->user()->role?->name, ['Sales','Manager']))
            <a href="/sales"
               class="flex items-center gap-3 p-3 rounded-lg border border-neutral-300 hover:border-yellow-500 transition mb-3">
                <div class="font-semibold text-lg">📄 Offertes</div>
            </a>
        @endif--}}


        {{-- 3. FACTUREN --}}
        @if(in_array(auth()->user()->role?->name, ['Finance','Manager']))
            <a href="/finance/facturen"
               class="flex items-center gap-3 p-3 rounded-lg border border-neutral-300 hover:border-yellow-500 transition mb-3">
                <div class="font-semibold text-lg">💶 Facturen</div>
            </a>
        @endif


        {{-- 4. INKOOP & VOORRAAD --}}
        @if(in_array(auth()->user()->role?->name, ['Inkoop','Manager']))
            <a href="/purchase"
               class="flex items-center gap-3 p-3 rounded-lg border border-neutral-300 hover:border-yellow-500 transition mb-3">
                <div class="font-semibold text-lg">📦 Voorraad & Inkoop</div>
            </a>
        @endif

        @if(in_array(auth()->user()->role?->name, ['maintenance']))
            <a href="/maintenance"
               class="flex items-center gap-3 p-3 rounded-lg border border-neutral-300 hover:border-yellow-500 transition mb-3">
                <div class="font-semibold text-lg">📦 Voorraad & Inkoop</div>
            </a>
        @endif

        {{-- 5. MAINTENANCE --}}
        @if(auth()->user()->can(abilities: 'access-maintenance'))


            {{-- Alleen hoofd maintenance (MaintenanceManager + Manager) --}}
            @can('maintenance-manager','maintenance')
                <a href="{{ route('maintenance.dashboard.manager') }}"
                class="flex items-center gap-3 p-3 rounded-lg border border-neutral-300 hover:border-yellow-500 transition mb-2">
                    <div class="font-semibold text-lg">🛠️ Maintenance</div>
                </a>
        @endcan




    {{-- Voor alle maintenance medewerkers --}}


@endif
@can('maintenance-manager')

@endcan






        {{-- 6. CONTRACTEN --}}
        @if(in_array(auth()->user()->role?->name, ['Finance','Manager']))
            <a href="/finance/contracten"
               class="flex items-center gap-3 p-3 rounded-lg border border-neutral-300 hover:border-yellow-500 transition mb-3">
                <div class="font-semibold text-lg">📑 Contracten</div>
            </a>
        @endif

    </flux:navlist>

    <flux:spacer />


    {{-- PROFILE / LOGOUT --}}
    <flux:dropdown class="mt-auto" position="bottom" align="start">
        <flux:profile
            :name="auth()->user()->name"
            :initials="auth()->user()->initials()"
            icon:trailing="chevrons-up-down"
            data-test="sidebar-menu-button"
        />

        <flux:menu class="w-[220px]">
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                            <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                {{ auth()->user()->initials() }}
                            </span>
                        </span>
                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings</flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    Log Out
                </flux:menu.item>
            </form>

        </flux:menu>
    </flux:dropdown>

</flux:sidebar>


<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
    <flux:spacer />
</flux:header>

{{ $slot }}

@fluxScripts
</body>
</html>
