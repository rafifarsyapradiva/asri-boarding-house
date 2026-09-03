<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Hunian kost eksklusif dengan fasilitas premium yang aman, tenteram, dan nyaman di Kota Semarang. Dirancang khusus untuk menunjang produktivitas mahasiswa dan profesional muda.">
    <title>@yield('title', \App\Models\Setting::get('logo_text') . ' - Hunian Kost Eksklusif')</title>
    
    <!-- Anti-Flicker theme detector -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    
    <!-- Global removeShimmer helper to prevent race condition -->
    <script>
        function removeShimmer(imgElement) {
            const placeholder = imgElement.previousElementSibling;
            if (placeholder && placeholder.classList.contains('shimmer-placeholder')) {
                placeholder.classList.add('opacity-0');
                setTimeout(() => {
                    placeholder.remove();
                }, 300);
            }
        }
    </script>

    <!-- Fonts: Space Grotesk for Headings, Plus Jakarta Sans for Body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;750;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Neo-Brutalism Global Custom Style -->
    <style>
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #ffffff;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', sans-serif;
        }
        /* Custom Hard Shadow Utilities - Responsive to prevent mobile overflow */
        .neo-btn-shadow {
            box-shadow: 2px 2px 0px 0px #000000;
        }
        @media (min-width: 640px) {
            .neo-btn-shadow {
                box-shadow: 4px 4px 0px 0px #000000;
            }
        }
        .neo-card-shadow {
            box-shadow: 4px 4px 0px 0px #000000;
        }
        @media (min-width: 768px) {
            .neo-card-shadow {
                box-shadow: 8px 8px 0px 0px #000000;
            }
        }
        .neo-card-shadow-yellow {
            box-shadow: 4px 4px 0px 0px #FACC15;
        }
        @media (min-width: 768px) {
            .neo-card-shadow-yellow {
                box-shadow: 8px 8px 0px 0px #FACC15;
            }
        }
        .neo-shadow-sm {
            box-shadow: 1.5px 1.5px 0px 0px #000000;
        }
        @media (min-width: 640px) {
            .neo-shadow-sm {
                box-shadow: 3px 3px 0px 0px #000000;
            }
        }
        
        /* Responsive Brutalist Borders to prevent viewport crowding on mobile */
        @media (max-width: 640px) {
            .border-4 {
                border-width: 2px !important;
            }
            .border-b-4 {
                border-bottom-width: 2px !important;
            }
            .border-t-4 {
                border-top-width: 2px !important;
            }
            .border-r-4 {
                border-right-width: 2px !important;
            }
            .border-l-4 {
                border-left-width: 2px !important;
            }
            .border-8 {
                border-width: 4px !important;
            }
            .border-t-8 {
                border-top-width: 4px !important;
            }
            .border-b-8 {
                border-bottom-width: 4px !important;
            }
        }
        
        /* Interactive button transitions - ONLY for hover on pointer devices */
        .neo-btn-interactive, .neo-btn-yellow-interactive {
            transition: transform 0.1s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.1s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        @media (hover: hover) {
            .neo-btn-interactive:hover {
                transform: translate(2px, 2px);
                box-shadow: 2px 2px 0px 0px #000000;
            }
            .neo-btn-yellow-interactive:hover {
                transform: translate(2px, 2px);
                box-shadow: 2px 2px 0px 0px #000000;
            }
            #wa-floating-button:hover {
                transform: translate(2px, 2px) !important;
                box-shadow: 2px 2px 0px 0px #000000 !important;
            }
        }

        /* Active touch styles for both touch and desktop click - stable position on click */
        .neo-btn-interactive:active, .neo-btn-yellow-interactive:active {
            transform: translate(2px, 2px) !important;
            box-shadow: 1px 1px 0px 0px #000000 !important;
        }

        /* Floating Button Layout - Safe bottom spacing for Mobile Bottom Navigation */
        #wa-floating-button {
            border: 4px solid #000000 !important;
            box-shadow: 4px 4px 0px 0px #000000 !important;
            border-radius: 0px !important;
            transition: all 0.2s ease-in-out;
            bottom: 5rem !important; /* Move up for bottom bar */
        }
        
        #guest-chat-toggle {
            bottom: 9.5rem !important; /* Move up for bottom bar & WA button */
        }
        
        #guest-chat-window {
            bottom: 14rem !important; /* Move up to clear bottom bar and toggle */
        }

        @media (min-width: 1024px) {
            #wa-floating-button {
                bottom: 1.5rem !important;
            }
            #guest-chat-toggle {
                bottom: 1.5rem !important;
                right: 6rem !important;
            }
            #guest-chat-window {
                bottom: 6rem !important;
                right: 6rem !important;
            }
        }

        /* WCAG AA Contrast & OLED Dark Mode Boosters */
        .dark body {
            background-color: #030712 !important; /* OLED black */
            color: #f9fafb !important;
        }
        .dark .bg-slate-900 {
            background-color: #030712 !important;
        }
        .dark .bg-slate-50 {
            background-color: #0b0f19 !important;
        }
        .dark .bg-white {
            background-color: #111827 !important; /* bg-gray-900 */
        }
        .dark .text-slate-500, .dark .text-gray-500 {
            color: #94a3b8 !important; /* slate-400 */
        }
        .dark .text-slate-600, .dark .text-gray-600 {
            color: #cbd5e1 !important; /* slate-300 */
        }
        .dark .text-slate-700, .dark .text-gray-700 {
            color: #e2e8f0 !important; /* slate-200 */
        }
        .dark .text-slate-800, .dark .text-gray-800 {
            color: #f1f5f9 !important; /* slate-100 */
        }

        /* Shimmer placeholder fade transition */
        .shimmer-placeholder {
            transition: opacity 0.3s ease-in-out !important;
        }

        /* Anti-FOUC (Flash of Unstyled Content) */
        .gsap-reveal {
            opacity: 0;
            visibility: hidden;
        }

        /* Brutalist Marquee Animation CSS */
        @keyframes marquee-scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
        .brutalist-marquee-container {
            overflow: hidden;
            white-space: nowrap;
            display: flex;
            width: 100%;
        }
        .brutalist-marquee-content {
            display: inline-block;
            flex-shrink: 0;
            min-width: 100%;
            animation: marquee-scroll 25s linear infinite;
        }

        /* Global Brutalist Dark Mode Accessibility Contrast Boosters */
        .dark .border-black {
            border-color: #ffffff !important;
        }
        .dark .border-b-4.border-black {
            border-bottom-color: #ffffff !important;
        }
        .dark .border-t-4.border-black {
            border-top-color: #ffffff !important;
        }
        .dark .border-r-4.border-black {
            border-right-color: #ffffff !important;
        }
        .dark .border-l-4.border-black {
            border-left-color: #ffffff !important;
        }
        .dark .border-2.border-black {
            border-color: #ffffff !important;
        }
        .dark .neo-card-shadow, .dark .shadow-\[4px_4px_0px_0px_rgba\(0\,0\,0\,1\)\] {
            box-shadow: 4px 4px 0px 0px #ffffff !important;
        }
        .dark .neo-btn-shadow {
            box-shadow: 2px 2px 0px 0px #ffffff !important;
        }
        @media (min-width: 640px) {
            .dark .neo-btn-shadow {
                box-shadow: 4px 4px 0px 0px #ffffff !important;
            }
        }
        @media (min-width: 768px) {
            .dark .neo-card-shadow, .dark .shadow-\[8px_8px_0px_0px_rgba\(0\,0\,0\,1\)\] {
                box-shadow: 8px 8px 0px 0px #ffffff !important;
            }
        }
        .dark .neo-shadow-sm {
            box-shadow: 1.5px 1.5px 0px 0px #ffffff !important;
        }
        @media (min-width: 640px) {
            .dark .neo-shadow-sm {
                box-shadow: 3px 3px 0px 0px #ffffff !important;
            }
        }
    </style>
</head>
<body class="antialiased flex flex-col min-h-screen text-black bg-white">

    @auth
        @if(auth()->user()->role === 'penyewa')
            @php
                $pendingReservasi = auth()->user()->latest_active_reservasi;
            @endphp
            @if($pendingReservasi && in_array($pendingReservasi->status, ['pending', 'dp', 'lunas']))
                <div class="bg-yellow-400 text-black border-b-4 border-black px-6 py-3.5 text-center font-black text-xs sm:text-sm uppercase tracking-wider relative z-50">
                    ⚠️ Anda memiliki reservasi Kamar {{ $pendingReservasi->kamar?->nomor_kamar ?? '-' }} yang belum aktif (Status: {{ strtoupper($pendingReservasi->status) }}). 
                    <a href="{{ route('penyewa.reservasi.pembayaran', $pendingReservasi->id) }}" class="underline hover:text-gray-800 ml-1.5 inline-block">
                        Klik di sini untuk melanjutkan pembayaran / melihat detail &rarr;
                    </a>
                </div>
            @endif
        @endif
    @endauth

    @include('layouts.navigation-landing')

    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Section: Neo-Brutalism Theme -->
    <footer class="bg-black text-white pt-16 pb-12 border-t-8 border-yellow-400">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-10">
            
            <!-- Column 1: Brand Info -->
            <div class="space-y-5 col-span-1 md:col-span-2">
                <a href="{{ route('landing.index') }}" id="footer-brand-logo" class="inline-flex items-center gap-3 bg-yellow-400 text-black border-4 border-black p-3.5 neo-btn-shadow font-black uppercase tracking-wider text-xl">
                    <span>{{ \App\Models\Setting::get('logo_icon') }}</span>
                    <span>{{ \App\Models\Setting::get('logo_text') }}</span>
                </a>
                <p class="text-sm text-gray-300 leading-relaxed max-w-md font-medium">
                    Hunian kost eksklusif dengan fasilitas premium yang aman, tenteram, dan nyaman di Kota Semarang. Dirancang khusus untuk menunjang produktivitas mahasiswa dan profesional muda.
                </p>
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <span class="inline-block py-1 px-3 border-2 border-white bg-black text-yellow-400 font-extrabold text-xs">
                        ⚡ WiFi CEPAT
                    </span>
                    <span class="inline-block py-1 px-3 border-2 border-white bg-black text-yellow-400 font-extrabold text-xs">
                        🔌 GRATIS LISTRIK
                    </span>
                </div>
            </div>

            <!-- Column 2: Tipe Kamar -->
            <div class="space-y-4">
                <h4 class="text-yellow-400 font-black text-base uppercase tracking-wider">Tipe Kamar</h4>
                <ul class="space-y-2.5 text-sm font-semibold">
                    <li><a href="{{ route('landing.kamar') }}?tipe_kamar=vip" id="footer-kamar-vip" class="hover:text-yellow-400 hover:underline transition">👑 Kamar VIP</a></li>
                    <li><a href="{{ route('landing.kamar') }}?tipe_kamar=deluxe" id="footer-kamar-deluxe" class="hover:text-yellow-400 hover:underline transition">✨ Kamar Deluxe</a></li>
                    <li><a href="{{ route('landing.kamar') }}?tipe_kamar=standar" id="footer-kamar-standar" class="hover:text-yellow-400 hover:underline transition">🏠 Kamar Standar</a></li>
                </ul>
            </div>

            <!-- Column 3: Hubungi Kami -->
            <div class="space-y-4">
                <h4 class="text-yellow-400 font-black text-base uppercase tracking-wider">Hubungi Kami</h4>
                <ul class="space-y-3.5 text-sm font-semibold">
                    <li class="flex items-start gap-2.5">
                        <span class="text-yellow-400">📍</span>
                        <span class="text-gray-300">{{ \App\Models\Setting::get('contact_address') }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-yellow-400">💬</span>
                        @php
                            $rawWa = \App\Models\Setting::get('contact_whatsapp') ?? config('reservasi.admin_wa') ?? '62895330031313';
                            $cleanWa = \App\Models\Setting::formatWhatsapp($rawWa);
                        @endphp
                        <a href="https://wa.me/{{ $cleanWa }}" target="_blank" rel="noopener noreferrer" id="footer-wa-link" class="text-white hover:text-yellow-400 hover:underline transition">{{ $rawWa }}</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-yellow-400">✉️</span>
                        <a href="mailto:{{ \App\Models\Setting::get('contact_email') }}" id="footer-email-link" class="text-white hover:text-yellow-400 hover:underline transition">{{ \App\Models\Setting::get('contact_email') }}</a>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Copyright bar -->
        <div class="max-w-6xl mx-auto px-6 border-t-4 border-gray-800 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-bold">
            <p>© 2026 {{ \App\Models\Setting::get('logo_text') }}. Hak Cipta Dilindungi.</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('landing.tentangKami') }}" id="footer-tentang-kami" class="hover:text-yellow-400 hover:underline transition">Tentang Kami</a>
                <span>•</span>
                <a href="{{ route('landing.testimoni') }}" id="footer-testimoni" class="hover:text-yellow-400 hover:underline transition">Testimoni</a>
                <span>•</span>
                <a href="{{ route('landing.galeri') }}" id="footer-galeri" class="hover:text-yellow-400 hover:underline transition">Galeri</a>
                <span>•</span>
                <a href="{{ route('landing.kamar') }}" id="footer-cari-kamar" class="hover:text-yellow-400 hover:underline transition">Cari Kamar</a>
                <span>•</span>
                <a href="{{ route('reservasi.login') }}" id="footer-login" class="hover:text-yellow-400 hover:underline transition">Portal Masuk</a>
                <span>•</span>
                <a href="{{ route('reservasi.register') }}" id="footer-register" class="hover:text-yellow-400 hover:underline transition">Daftar Akun</a>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <x-wa-float-button :wa-number="$waNumber" :logo-text="$logoText" :message="View::hasSection('wa_custom_message') ? View::yieldContent('wa_custom_message') : null" />

    <!-- Guest Chat Widget -->
    <x-guest-chat-widget />

    <!-- Mobile Bottom Navigation Bar (Thumb-Zone Design) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white dark:bg-slate-900 border-t-4 border-black dark:border-white h-16 flex items-center justify-around px-2 py-1 text-black dark:text-white">
        <!-- Beranda -->
        <a href="{{ route('landing.index') }}" class="flex flex-col items-center justify-center w-14 h-12 hover:text-yellow-600 dark:hover:text-yellow-400 transition active:scale-95">
            <span class="text-xl">🏠</span>
            <span class="text-[9px] font-black uppercase tracking-wider mt-0.5">Beranda</span>
        </a>
        
        <!-- Fasilitas -->
        <a href="{{ route('landing.fasilitas') }}" class="flex flex-col items-center justify-center w-14 h-12 hover:text-yellow-600 dark:hover:text-yellow-400 transition active:scale-95">
            <span class="text-xl">✨</span>
            <span class="text-[9px] font-black uppercase tracking-wider mt-0.5">Fasilitas</span>
        </a>
        
        <!-- Cari Kamar -->
        <a href="{{ route('landing.kamar') }}" class="flex flex-col items-center justify-center w-14 h-12 hover:text-yellow-600 dark:hover:text-yellow-400 transition active:scale-95">
            <span class="text-xl">🔍</span>
            <span class="text-[9px] font-black uppercase tracking-wider mt-0.5">Cari</span>
        </a>
        
        <!-- Tanya Admin (Triggers Guest Chat Widget) -->
        <button onclick="const chatToggle = document.getElementById('guest-chat-toggle'); if(chatToggle) chatToggle.click();" 
                class="flex flex-col items-center justify-center w-14 h-12 hover:text-yellow-600 dark:hover:text-yellow-400 transition focus:outline-none active:scale-95 cursor-pointer">
            <span class="text-xl">💬</span>
            <span class="text-[9px] font-black uppercase tracking-wider mt-0.5">Chat</span>
        </button>
        
        <!-- Akun / Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-14 h-12 hover:text-yellow-600 dark:hover:text-yellow-400 transition active:scale-95">
            <span class="text-xl">👤</span>
            <span class="text-[9px] font-black uppercase tracking-wider mt-0.5">Akun</span>
        </a>
    </div>

    <!-- GSAP Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>

    <!-- Custom GSAP Animations -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Register ScrollTrigger plugin
            gsap.registerPlugin(ScrollTrigger);

            // Responsive Animations via matchMedia
            const mm = gsap.matchMedia();

            // DESKTOP Only Animations (>= 1024px)
            mm.add("(min-width: 1024px)", () => {
                // 1. Magnetic Buttons - Prevent shift during mousedown/click
                const magneticElements = document.querySelectorAll('.neo-btn-interactive, .neo-btn-yellow-interactive');
                magneticElements.forEach(el => {
                    let isMouseDown = false;

                    el.addEventListener('mousedown', () => {
                        isMouseDown = true;
                        gsap.to(el, {
                            x: 0,
                            y: 0,
                            duration: 0.1,
                            overwrite: "auto"
                        });
                    });

                    el.addEventListener('mouseup', () => {
                        isMouseDown = false;
                    });

                    el.addEventListener('mousemove', (e) => {
                        if (isMouseDown) return;
                        const rect = el.getBoundingClientRect();
                        const x = e.clientX - rect.left - (rect.width / 2);
                        const y = e.clientY - rect.top - (rect.height / 2);
                        
                        // Move button slightly towards cursor
                        gsap.to(el, {
                            x: x * 0.3,
                            y: y * 0.3,
                            duration: 0.3,
                            ease: "power2.out",
                            overwrite: "auto"
                        });
                    });
                    
                    el.addEventListener('mouseleave', () => {
                        isMouseDown = false;
                        // Snap back with elastic spring
                        gsap.to(el, {
                            x: 0,
                            y: 0,
                            duration: 0.6,
                            ease: "elastic.out(1.2, 0.4)",
                            overwrite: "auto"
                        });
                    });
                });

                // 2. Elastic Hover on Cards
                const cards = document.querySelectorAll('.gallery-card');
                cards.forEach(card => {
                    card.addEventListener('mouseenter', () => {
                        gsap.to(card, {
                            y: -6,
                            x: -2,
                            boxShadow: "10px 10px 0px 0px #000000",
                            duration: 0.4,
                            ease: "back.out(1.7)"
                        });
                    });
                    card.addEventListener('mouseleave', () => {
                        gsap.to(card, {
                            y: 0,
                            x: 0,
                            boxShadow: "8px 8px 0px 0px #000000",
                            duration: 0.4,
                            ease: "back.out(1.7)"
                        });
                    });
                });

                // 3. Scroll Reveal for Cards (staggered + rotation)
                document.querySelectorAll('.gsap-scroll-reveal-container').forEach(container => {
                    const reveals = container.querySelectorAll('.gsap-scroll-reveal');
                    if (reveals.length > 0) {
                        gsap.from(reveals, {
                            scrollTrigger: {
                                trigger: container,
                                start: "top 85%",
                                toggleActions: "play none none none"
                            },
                            opacity: 0,
                            y: 60,
                            rotate: -2,
                            scale: 0.95,
                            duration: 0.8,
                            stagger: 0.15,
                            ease: "back.out(1.5)"
                        });
                    }
                });
            });

            // MOBILE Only Animations (< 1024px)
            mm.add("(max-width: 1023px)", () => {
                // 1. Simple Card Scroll Reveal without heavy rotation/scale for smooth performance
                document.querySelectorAll('.gsap-scroll-reveal-container').forEach(container => {
                    const reveals = container.querySelectorAll('.gsap-scroll-reveal');
                    if (reveals.length > 0) {
                        gsap.from(reveals, {
                            scrollTrigger: {
                                trigger: container,
                                start: "top 90%",
                                toggleActions: "play none none none"
                            },
                            opacity: 0,
                            y: 30,
                            duration: 0.6,
                            stagger: 0.1,
                            ease: "power2.out"
                        });
                    }
                });
            });

            // GLOBAL Animations (All screen sizes)
            // 1. Hero Text & Image Reveal on load
            const tl = gsap.timeline();
            
            // Set initially hidden elements to visible (anti-FOUC)
            gsap.set(".gsap-reveal", { visibility: "visible", opacity: 1 });
            
            tl.from(".hero-tagline", {
                y: -30,
                opacity: 0,
                duration: 0.6,
                ease: "back.out(1.5)",
                delay: 0.2
            })
            .from(".hero-title-line", {
                y: "100%",
                opacity: 0,
                duration: 0.8,
                ease: "power4.out"
            }, "-=0.3")
            .from(".hero-description", {
                y: 20,
                opacity: 0,
                duration: 0.6,
                ease: "power2.out"
            }, "-=0.4")
            .from(".hero-cta-btn", {
                scale: 0.9,
                opacity: 0,
                duration: 0.5,
                stagger: 0.1,
                ease: "back.out(1.8)"
            }, "-=0.3")
            .from(".hero-image-container", {
                opacity: 0,
                x: 50,
                rotate: 3,
                duration: 0.8,
                ease: "power3.out"
            }, "-=0.5");
        });
    </script>



    <!-- WhatsApp Analytics Conversion Tracking -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', function (e) {
                const anchor = e.target.closest('a');
                if (anchor && anchor.href && anchor.href.includes('wa.me')) {
                    // Determine click source
                    let source = 'unknown';
                    let roomId = null;
                    
                    if (anchor.id === 'wa-floating-button') {
                        source = 'floating_button';
                    } else if (anchor.id === 'footer-wa-link') {
                        source = 'footer';
                    } else if (anchor.id === 'btn-whatsapp-admin-faq') {
                        source = 'faq_page';
                    } else if (anchor.id === 'btn-whatsapp-admin-cta') {
                        source = 'cara_booking_cta';
                    } else if (anchor.id === 'lightbox-wa-btn') {
                        source = 'gallery_lightbox';
                    } else if (anchor.classList.contains('kamar-action-btn')) {
                        source = 'room_card';
                        const card = anchor.closest('.kamar-card');
                        if (card) {
                            roomId = card.getAttribute('data-id');
                        }
                    } else if (anchor.closest('.promo-card')) {
                        source = 'promo_package';
                    } else if (anchor.id === 'cta-wa-link') {
                        source = 'gallery_cta';
                    } else if (anchor.getAttribute('href') && anchor.getAttribute('href').includes('galeri')) {
                        source = 'gallery_page';
                    } else if (anchor.getAttribute('href') && anchor.getAttribute('href').includes('tentang-kami')) {
                        source = 'about_page';
                    } else {
                        if (anchor.closest('footer')) {
                            source = 'footer';
                        } else if (anchor.closest('#room-tour')) {
                            source = 'room_tour';
                        }
                    }

                    // Send tracking beacon
                    fetch('{{ route('analytics.track-whatsapp') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            source: source,
                            kamar_id: roomId
                        }),
                        keepalive: true
                    }).catch(err => console.error('Analytics tracking failed', err));
                }
            });
        });
    </script>
</body>
</html>
