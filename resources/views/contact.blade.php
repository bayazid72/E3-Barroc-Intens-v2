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



</head>
<body>
         <nav class="w-full flex items-center justify-between bg-white border-b border-gray-200">

        <!-- Gele balk + logo -->
        <div class="bg-[#FFD600] h-24 w-56 flex items-center justify-center">
            <img src="/img/Logo2_groot.png" alt="logo" class="h-16">
        </div>

        <!-- Navigatie links-->
        <div class="flex space-x-10 text-2xl font-semibold">
           <!-- <a href="#" class="hover:underline">Producten</a>-->
            <a href="/contact" class="hover:underline">Contact</a>
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


  <div class="max-w-xl mx-auto mt-20 px-4">

    <h1 class="text-5xl font-semibold mb-10 tracking-wide">CONTACT ONS!</h1>

    <form class="space-y-5">

        <div>
            <label class="block text-lg font-medium mb-1" for="naam">Naam:</label>
            <input type="text" id="naam" name="naam"
                class="w-full border border-gray-300 px-4 py-3 text-lg focus:outline-none focus:border-gray-500"
                placeholder="Jan Willem">
        </div>

        <div>
            <label class="block text-lg font-medium mb-1" for="bedrijf">Bedrijfsnaam:</label>
            <input type="text" id="bedrijf" name="bedrijf"
                class="w-full border border-gray-300 px-4 py-3 text-lg focus:outline-none focus:border-gray-500"
                placeholder="Jan Willem BV">
        </div>

        <div>
            <label class="block text-lg font-medium mb-1" for="postcode">Postcode:</label>
            <input type="text" id="postcode" name="postcode"
                class="w-full border border-gray-300 px-4 py-3 text-lg focus:outline-none focus:border-gray-500"
                placeholder="1234JW">
        </div>

        <div>
            <label class="block text-lg font-medium mb-1" for="telefoon">Telefoonnummer:</label>
            <input type="tel" id="telefoon" name="telefoon"
                class="w-full border border-gray-300 px-4 py-3 text-lg focus:outline-none focus:border-gray-500"
                placeholder="06 12345678">
        </div>

        <div>
            <label class="block text-lg font-medium mb-1" for="email">Uw e-mail:</label>
            <input type="email" id="email" name="email"
                class="w-full border border-gray-300 px-4 py-3 text-lg focus:outline-none focus:border-gray-500"
                placeholder="janwillem@email.com">
        </div>

        <div>
            <label class="block text-lg font-medium mb-1" for="titel">Titel:</label>
            <input type="text" id="titel" name="titel"
                class="w-full border border-gray-300 px-4 py-3 text-lg focus:outline-none focus:border-gray-500"
                placeholder="Huren van koffiemachine">
        </div>

        <div>
            <label class="block text-lg font-medium mb-1" for="bericht">Schrijf mail</label>
            <textarea id="bericht" name="bericht"
                class="w-full border border-gray-300 px-4 py-3 text-lg min-h-[180px] focus:outline-none focus:border-gray-500"></textarea>
        </div>

        <button type="submit"
            class="bg-[#FFD600] text-black text-xl font-semibold px-8 py-3 mt-4 hover:opacity-90">
            Verstuur mail
        </button>

    </form>
</div>


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
