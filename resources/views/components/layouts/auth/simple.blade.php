<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-[#f3f4f6] antialiased">

    <!-- Center container -->
    <div class="flex min-h-screen flex-col items-center justify-center p-6">

        <!-- Logo (GROOT en gecentreerd) -->
        <div class="flex flex-col items-center mb-8">
            <x-app-logo-icon class="w-20 h-20 text-black" />
        </div>

        <!-- White Card -->
        <div class="w-full max-w-xl bg-white shadow-lg rounded-xl p-10">
            {{ $slot }}
        </div>

    </div>

    @fluxScripts
</body>
</html>
