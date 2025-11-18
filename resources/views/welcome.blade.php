<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Barroc Intens</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600&display=swap">

    <style>
        body { font-family: 'Oswald', sans-serif; }
    </style>
</head>

<body class="bg-white text-black">

    <!-- NAVBAR -->
     <nav class="w-full flex items-center justify-between bg-white border-b border-gray-200">

        <!-- Gele balk + logo -->
        <div class="bg-[#FFD600] h-24 w-56 flex items-center justify-center">
            <img src="/img/Logo2_groot.png" alt="logo" class="h-16">
        </div>

        <!-- Navigatie links-->
        <div class="flex space-x-10 text-2xl font-semibold">
           <!-- <a href="#" class="hover:underline">Producten</a>-->
            <a href="/contact" class="bg-[#FFD600] px-6 py-3 text-xl font-semibold">
                Contact</a>
        </div>

        <!-- Dashboard / Login / Register knop -->
        @if (Route::has('login'))
            <div class="flex space-x-4 items-center pr-6">

                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-[#FFD600] px-6 py-3 text-xl font-semibold">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-[#FFD600] px-6 py-3 text-xl font-semibold">
                        Login
                    </a>

                    @if (Route::has('register'))

                    @endif
                @endauth

            </div>
        @endif


    </nav>

    <!-- MAIN CONTENT -->
    <section class="px-44 mt-16">
        <h1 class="text-5xl mb-6 font-semibold">Over Barroc Intens</h1>

        <p class="text-xl leading-relaxed w-[55rem]">
            Het bedrijf “Barroc Intens” is leverancier van koffieautomaten. Dit bedrijf verhuurt koffiemachines aan horecagelegenheden.
            Verhuur begint met een gesprek waarbij de behoeften van de klant worden ingevuld op een automatisch intake formulier.
            Daaruit komt voor de klant automatisch een offerte en contract per e-mail. Wanneer dit contract digitaal wordt ondertekend wordt
            de order verwerkt en wordt de factuur aangemaakt en per e-mail verstuurd. Na betaling van de factuur kan het logistieke proces beginnen.
            Daarna wordt de machine uitgeleverd en geïnstalleerd. Dit dient door de klant voor levering te worden afgetekend.
            Zodra de levering is voltooid wordt het periodieke onderhoud van de machine ingepland.
            Dit geheel moet via een backend worden geautomatiseerd.
        </p>
    </section>

    <!-- SCHEIDSLIJN -->
    <div class="w-full border-b-4 border-[#FFD600] mt-20"></div>

    <!-- ONDERSTE BLOKKEN -->
    <section class="grid grid-cols-3 text-center text-4xl py-16 px-32 gap-12">
        <div>Producten</div>
        <div>Machines</div>
        <div>Over ons</div>
    </section>

    <div class="w-full flex justify-end px-32 pb-20">
        <a href="#" class="bg-[#FFD600] px-6 py-3 text-xl">
            Contact
        </a>
    </div>

</body>
</html>
