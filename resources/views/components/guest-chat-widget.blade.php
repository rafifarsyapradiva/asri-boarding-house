<div id="guest-chat-widget" 
     class="relative"
     data-start-route="{{ route('guest-chat.start') }}"
     data-messages-route="{{ route('guest-chat.messages') }}"
     data-send-route="{{ route('guest-chat.send') }}"
     data-csrf-token="{{ csrf_token() }}">
    <!-- Floating Toggle Button -->
    <button id="guest-chat-toggle"
            aria-label="Tanya Admin Kost"
            class="fixed bottom-24 right-4 sm:bottom-6 sm:right-24 z-50 flex items-center justify-center w-14 h-14 bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black shadow-[4px_4px_0px_0px_#000000] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#000000] transition-all duration-200 cursor-pointer">
        <span class="text-2xl" id="toggle-icon">💬</span>
    </button>

    <!-- Chat Panel Window -->
    <div id="guest-chat-window"
         class="fixed bottom-[168px] sm:bottom-24 right-4 sm:right-24 z-50 w-[calc(100vw-2rem)] max-w-[360px] h-[480px] bg-white border-4 border-black shadow-[8px_8px_0px_0px_#000000] flex flex-col transition-all duration-300 transform translate-y-10 opacity-0 pointer-events-none">
        
        <!-- Panel Header -->
        <div class="bg-yellow-400 text-black border-b-4 border-black p-4 flex justify-between items-center font-black uppercase tracking-wider text-xs sm:text-sm shrink-0">
            <div class="flex items-center gap-2">
                <span>⚡</span>
                <span>Tanya Admin Kost</span>
            </div>
            <button id="guest-chat-close" class="text-lg font-black hover:text-red-600 transition cursor-pointer">✕</button>
        </div>

        <!-- Panel Body (Dynamic State) -->
        <div class="flex-1 flex flex-col overflow-hidden bg-slate-50" id="guest-chat-body">
            
            <!-- STATE 1: Start Form -->
            <div id="guest-state-form" class="flex-1 p-5 flex flex-col justify-center space-y-4">
                <div class="text-center pb-2">
                    <span class="text-3xl block mb-2">👋</span>
                    <h4 class="font-black uppercase text-xs sm:text-sm tracking-tight">Ada Pertanyaan?</h4>
                    <p class="text-[11px] font-bold text-slate-500 mt-1 leading-relaxed">
                        Tanyakan apa saja seputar Asri Boarding House secara langsung pada admin kami di sini!
                    </p>
                </div>

                <div class="space-y-3">
                    <div>
                        <label for="guest-name" class="block text-[10px] font-black uppercase tracking-wider text-black mb-1">Nama Lengkap</label>
                        <input type="text" id="guest-name" placeholder="Masukkan nama Anda..." autocomplete="off"
                               class="w-full px-3 py-2 border-4 border-black bg-white text-xs font-bold text-black placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-yellow-400">
                    </div>
                    <div>
                        <label for="guest-phone" class="block text-[10px] font-black uppercase tracking-wider text-black mb-1">Nomor WhatsApp</label>
                        <input type="text" id="guest-phone" placeholder="Contoh: 0812XXXXXXXX" autocomplete="off"
                               class="w-full px-3 py-2 border-4 border-black bg-white text-xs font-bold text-black placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-yellow-400">
                    </div>
                </div>

                <div id="form-error" class="hidden text-[10px] font-black uppercase tracking-wider bg-red-500 text-white border-2 border-black p-2 text-center shadow-[2px_2px_0px_0px_#000000]">
                    Tolong isi data dengan benar.
                </div>

                <button id="guest-start-btn"
                        class="w-full bg-black hover:bg-yellow-400 text-white hover:text-black font-black py-2.5 border-4 border-black shadow-[3px_3px_0px_0px_#000000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[2px_2px_0px_0px_#000000] transition duration-150 text-xs uppercase tracking-wider cursor-pointer">
                    Mulai Chat 🚀
                </button>
            </div>

            <!-- STATE 2: Active Chat -->
            <div id="guest-state-chat" class="hidden flex-1 flex flex-col overflow-hidden">
                <!-- Message List -->
                <div id="guest-messages-container" class="flex-1 overflow-y-auto p-4 space-y-4 flex flex-col">
                    <!-- Messages render here -->
                </div>

                <!-- Info/Warning Banner if Chat Closed -->
                <div id="guest-closed-banner" class="hidden bg-yellow-100 text-yellow-900 p-2 border-t-2 border-b-2 border-black text-center text-[10px] font-bold uppercase tracking-wider shrink-0">
                    🔒 Chat telah diselesaikan. Kirim pesan untuk membuka kembali.
                </div>

                <!-- Message Input Form -->
                <div id="guest-chat-input-container" class="p-3 border-t-4 border-black bg-white flex gap-2 items-center shrink-0">
                    <input type="text" id="guest-message-input" placeholder="Tulis pertanyaan Anda..." autocomplete="off" maxlength="1000"
                           class="flex-grow px-3 py-2 border-4 border-black bg-white text-xs font-bold text-black placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-yellow-400 disabled:opacity-75 disabled:cursor-not-allowed">
                    <button id="guest-send-btn"
                            class="bg-black hover:bg-yellow-400 text-white hover:text-black border-4 border-black font-black w-10 h-10 flex items-center justify-center shadow-[2px_2px_0px_0px_#000000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[1px_1px_0px_0px_#000000] transition cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                        ✈️
                    </button>
                </div>

                <!-- Readonly Closed Container -->
                <div id="guest-closed-container" class="hidden p-4 border-t-4 border-black bg-red-100 text-red-600 text-center text-xs font-black uppercase tracking-wider shrink-0">
                    🔒 Obrolan ini telah ditutup secara permanen oleh Admin.
                </div>
            </div>

        </div>
    </div>
</div>

@vite(['resources/js/components/guest-chat.js'])

