<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Portal Masuk') - {{ \App\Models\Setting::get('logo_text', 'Asri Boarding House') }}</title>

        <!-- Fonts: Space Grotesk for Headings, Plus Jakarta Sans for Body -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;750;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Anti-Flicker theme detector -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="antialiased bg-slate-50 dark:bg-slate-950 text-black dark:text-white min-h-screen">
        <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            
            <!-- Brutalist Background Geometric Grid Lines -->
            <div class="absolute inset-0 bg-[radial-gradient(#94a3b8_1px,transparent_1px)] dark:bg-[radial-gradient(#334155_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none opacity-30"></div>
            
            <div class="w-full {{ $size === 'lg' ? 'lg:max-w-4xl' : 'sm:max-w-md' }} relative z-10 transition-all duration-300">
                <!-- Branding Header inside the container -->
                <div class="flex flex-col items-center mb-6 {{ $size === 'lg' ? 'lg:hidden' : '' }}">
                    <a href="{{ route('landing.index') }}" class="flex flex-col items-center gap-2 group">
                        <div class="px-4 py-2 bg-yellow-400 text-black border-4 border-black dark:border-white font-black uppercase tracking-wider text-sm shadow-[3px_3px_0px_0px_#000000] dark:shadow-[3px_3px_0px_0px_#ffffff]">
                            {{ \App\Models\Setting::get('logo_icon', '🏠') }} {{ \App\Models\Setting::get('logo_text', 'ASRI') }}
                        </div>
                    </a>
                </div>

                <!-- Neo-Brutalism Container Box -->
                <div class="w-full {{ $size === 'lg' ? 'p-0' : 'p-6 sm:p-8' }} bg-white dark:bg-slate-900 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] sm:shadow-[8px_8px_0px_0px_#000000] sm:dark:shadow-[8px_8px_0px_0px_#ffffff] rounded-none overflow-hidden">
                    {{ $slot }}
                </div>

                <!-- Footer Back to Home Link -->
                <div class="text-center mt-8">
                    <a href="{{ route('landing.index') }}" class="inline-block px-4 py-2 bg-white dark:bg-slate-900 text-black dark:text-white border-2 border-black dark:border-white font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#000000] dark:hover:shadow-[1px_1px_0px_0px_#ffffff] transition-all">
                        ← Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
