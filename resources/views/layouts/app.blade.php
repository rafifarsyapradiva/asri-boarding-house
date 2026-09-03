<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts: Space Grotesk for Headings, Plus Jakarta Sans for Body -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;750;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;750;800&display=swap" rel="stylesheet">
        </noscript>

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

        <!-- Neo-Brutalism Global Custom Style -->
        <style>
            body {
                font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Space Grotesk', sans-serif;
            }
            [x-cloak] { display: none !important; }

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
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }" x-effect="sidebarOpen; if(window.toggleScrollLock) setTimeout(() => window.toggleScrollLock(), 50)">
        <style>
            [x-cloak] { display: none !important; }
        </style>
        @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'penyewa'))
            <div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex flex-col lg:flex-row overflow-x-hidden">
                <!-- Sidebar -->
                @include('layouts.admin-sidebar')

                <!-- Main Content Area -->
                <div class="flex-1 flex flex-col min-h-screen lg:pl-64">
                    <!-- Topbar -->
                    @include('layouts.admin-topbar')

                    <!-- Page Heading -->
                    @isset($header)
                        @if($isBrutalistHeader)
                            <header class="bg-yellow-400 border-b-4 border-black py-8 relative z-10 text-black">
                                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @else
                            <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 py-6 px-4 sm:px-6 lg:px-8 shadow-sm">
                                <div class="max-w-7xl mx-auto">
                                    {{ $header }}
                                </div>
                            </header>
                        @endif
                    @endisset

                    <!-- Page Content -->
                    <main class="flex-1 bg-slate-50 dark:bg-slate-900">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        @else
            <div class="min-h-screen bg-gray-100">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-yellow-400 border-b-4 border-black py-8 relative z-10">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>
        @endif

        <!-- Rupiah Input Formatter Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const rupiahInputs = document.querySelectorAll('.rupiah-input');

                function formatRupiah(value) {
                    if (!value && value !== 0) return '';
                    let clean = value.toString().replace(/[^0-9]/g, '');
                    if (!clean) return '';
                    return clean.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }

                rupiahInputs.forEach(function (displayInput) {
                    const targetId = displayInput.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);

                    // Initialize value on load
                    if (displayInput.value) {
                        displayInput.value = formatRupiah(displayInput.value);
                    }

                    displayInput.addEventListener('input', function () {
                        let cursorPosition = this.selectionStart;
                        let originalValue = this.value;
                        let clean = originalValue.replace(/[^0-9]/g, '');

                        // Sync raw value to hidden input
                        if (targetInput) {
                            targetInput.value = clean;
                        }

                        let formatted = formatRupiah(clean);
                        this.value = formatted;

                        // Calculate new cursor position
                        let textBeforeCursor = originalValue.substring(0, cursorPosition);
                        let digitsBeforeCursor = textBeforeCursor.replace(/[^0-9]/g, '').length;

                        let newCursorPosition = 0;
                        let digitCount = 0;
                        for (let i = 0; i < formatted.length; i++) {
                            if (formatted[i] !== '.') {
                                digitCount++;
                            }
                            if (digitCount === digitsBeforeCursor) {
                                newCursorPosition = i + 1;
                                break;
                            }
                        }
                        if (digitsBeforeCursor === 0) {
                            newCursorPosition = 0;
                        }
                        this.setSelectionRange(newCursorPosition, newCursorPosition);
                    });
                });
            });
        </script>
        <!-- Global Neo-Brutalist Custom Confirm Modal -->
        <div x-data="{ 
                 open: false, 
                 title: '', 
                 message: '', 
                 confirmLabel: 'Ya, Lanjutkan',
                 cancelLabel: 'Batal',
                 isDanger: false,
                 confirmCallback: null,
                 show(detail) {
                     this.title = detail.title || 'Konfirmasi Tindakan';
                     this.message = detail.message || 'Apakah Anda yakin?';
                     this.confirmCallback = detail.callback;
                     this.confirmLabel = detail.options?.confirmLabel || 'Ya, Lanjutkan';
                     this.cancelLabel = detail.options?.cancelLabel || 'Batal';
                     this.isDanger = detail.options?.isDanger !== false; // default to true
                     this.open = true;
                 },
                 triggerConfirm() {
                     if (this.confirmCallback) this.confirmCallback();
                     this.open = false;
                 }
             }"
             x-effect="open; if(window.toggleScrollLock) setTimeout(() => window.toggleScrollLock(), 50)"
             @brutalist-confirm.window="show($event.detail)"
             x-show="open"
             x-cloak
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             style="display: none;">
            <div class="w-full max-w-md bg-white dark:bg-slate-900 border-4 border-black dark:border-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] p-6 rounded-none relative"
                 @click.away="open = false">
                <!-- Modal Title -->
                <h3 class="text-base font-black text-black dark:text-white uppercase tracking-wider mb-2 border-b-4 border-black dark:border-white pb-2 flex items-center gap-2">
                    <template x-if="isDanger">
                        <span class="text-red-500 text-lg">⚠️</span>
                    </template>
                    <span x-text="title">Konfirmasi</span>
                </h3>
                <!-- Modal Message -->
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 leading-relaxed mb-6" x-text="message">
                    Apakah Anda yakin ingin melakukan tindakan ini?
                </p>
                <!-- Modal Actions -->
                <div class="flex justify-end gap-3 border-t-2 border-black dark:border-white pt-4">
                    <button type="button" @click="open = false" 
                            class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 text-slate-900 font-extrabold py-2 px-4 border-2 border-black dark:border-white rounded-none shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] active:translate-x-[0px] active:translate-y-[0px] active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all duration-150 text-xs uppercase tracking-wider gap-2 cursor-pointer">
                        <span x-text="cancelLabel">Batal</span>
                    </button>
                    <button type="button" @click="triggerConfirm" 
                            :class="isDanger ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-yellow-400 hover:bg-yellow-500 text-black'"
                            class="inline-flex items-center justify-center font-black py-2 px-4 border-2 border-black dark:border-white rounded-none shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] active:translate-x-[0px] active:translate-y-[0px] active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all duration-150 text-xs uppercase tracking-wider gap-2 cursor-pointer">
                        <span x-text="confirmLabel">Ya, Lanjutkan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Global Neo-Brutalist Custom Toast Notification Container -->
        <div id="global-toast-container" class="fixed top-5 right-5 z-[9999] pointer-events-none flex flex-col gap-3"></div>

        <script>
            // Global scroll lock helper to prevent mobile layout scrolling when overlay is active
            window.toggleScrollLock = function() {
                if (window.innerWidth >= 1024) {
                    document.body.classList.remove('overflow-hidden');
                    return;
                }
                const mobileSidebar = document.querySelector('.brutalist-sidebar');
                const isSidebarVisible = mobileSidebar && !mobileSidebar.classList.contains('-translate-x-full');
                
                const chatWindow = document.getElementById('guest-chat-window');
                const isChatVisible = chatWindow && !chatWindow.classList.contains('pointer-events-none');
                
                const activeModal = document.querySelector('[x-show="open"]:not([style*="display: none"]), [x-show="modalOpen"]:not([style*="display: none"])');
                
                if (isSidebarVisible || isChatVisible || activeModal) {
                    document.body.classList.add('overflow-hidden');
                } else {
                    document.body.classList.remove('overflow-hidden');
                }
            };
            
            // Listen for window resize to unlock body if screen size changes
            window.addEventListener('resize', window.toggleScrollLock);

            // Helper JS Global
            window.brutalistConfirm = function(title, message, callback, options = {}) {
                window.dispatchEvent(new CustomEvent('brutalist-confirm', {
                    detail: { title, message, callback, options }
                }));
            };

            // Global Toast helper (Safe DOM Construction - XSS Protected)
            window.showToast = function(message, type = 'success') {
                const container = document.getElementById('global-toast-container');
                if (!container) return;
                
                const toastId = 'toast-' + Date.now();
                const icon = type === 'success' ? '✅' : '❌';
                const title = type === 'success' ? 'Sukses' : 'Gagal';
                
                const toastEl = document.createElement('div');
                toastEl.id = toastId;
                toastEl.className = 'flex items-center gap-3 p-4 border-4 border-black dark:border-white bg-white dark:bg-slate-900 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-[-20px] opacity-0 rounded-none';
                
                const iconDiv = document.createElement('div');
                iconDiv.className = 'flex-shrink-0 text-xl';
                iconDiv.textContent = icon;
                
                const bodyDiv = document.createElement('div');
                bodyDiv.className = 'flex-1 text-black dark:text-white';
                
                const titleP = document.createElement('p');
                titleP.className = 'text-xs font-black tracking-wide uppercase opacity-75';
                titleP.textContent = title;
                
                const msgP = document.createElement('p');
                msgP.className = 'text-xs font-bold';
                msgP.textContent = message;
                
                bodyDiv.appendChild(titleP);
                bodyDiv.appendChild(msgP);
                
                const closeBtn = document.createElement('button');
                closeBtn.type = 'button';
                closeBtn.className = 'flex-shrink-0 text-black dark:text-white hover:opacity-75 focus:outline-none ml-2 cursor-pointer';
                closeBtn.innerHTML = '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>';
                closeBtn.addEventListener('click', function() {
                    toastEl.remove();
                });
                
                toastEl.appendChild(iconDiv);
                toastEl.appendChild(bodyDiv);
                toastEl.appendChild(closeBtn);
                
                container.appendChild(toastEl);
                
                setTimeout(() => {
                    toastEl.classList.remove('translate-y-[-20px]', 'opacity-0');
                    toastEl.classList.add('translate-y-0', 'opacity-100');
                }, 50);
                
                setTimeout(() => {
                    if (toastEl && toastEl.parentNode) {
                        toastEl.classList.remove('translate-y-0', 'opacity-100');
                        toastEl.classList.add('translate-y-[-20px]', 'opacity-0');
                        setTimeout(() => {
                            if (toastEl && toastEl.parentNode) {
                                toastEl.remove();
                            }
                        }, 300);
                    }
                }, 4000);
            };

            // Form Submit Interceptor
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.hasAttribute('data-confirm')) {
                    if (form.dataset.confirmed === 'true') {
                        return; // proceed with submission
                    }
                    e.preventDefault();
                    const message = form.getAttribute('data-confirm');
                    const title = form.getAttribute('data-title') || 'Konfirmasi Tindakan';
                    const confirmLabel = form.getAttribute('data-confirm-label') || 'Ya, Lanjutkan';
                    const isDanger = form.getAttribute('data-confirm-danger') !== 'false';
                    
                    window.brutalistConfirm(title, message, () => {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }, {
                        confirmLabel: confirmLabel,
                        isDanger: isDanger
                    });
                }
            });
        </script>
        @stack('scripts')
    </body>
</html>
