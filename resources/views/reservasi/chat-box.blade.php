<!-- Wrapper chat box menggunakan data attributes untuk konfigurasi (Decoupling Javascript & Blade) -->
<div id="chat-box-app" 
     class="w-full flex flex-col bg-white dark:bg-slate-950 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] md:shadow-[6px_6px_0px_0px_#000000] md:dark:shadow-[6px_6px_0px_0px_#ffffff] overflow-hidden"
     data-reservasi-id="{{ $reservasi->id }}"
     data-user-role="{{ auth()->user()->role ?? 'penyewa' }}"
     data-fetch-url="{{ route('api.chat.fetch', $reservasi->id) }}"
     data-send-url="{{ route('api.chat.send', $reservasi->id) }}"
     data-csrf-token="{{ csrf_token() }}">
     
    <!-- Header -->
    <div class="px-5 py-4 border-b-4 border-black dark:border-b-white bg-yellow-400 text-black flex justify-between items-center">
        <div class="flex items-center gap-2">
            <span class="text-base">💬</span>
            <h3 class="font-black text-black uppercase tracking-tight text-sm">Obrolan Diskusi Reservasi</h3>
        </div>
        <span class="text-xs font-black bg-white dark:bg-slate-800 text-black dark:text-white border-2 border-black dark:border-white px-2.5 py-1 shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]">ID: {{ $reservasi->order_id }}</span>
    </div>

    <!-- Error Banner -->
    <div id="chat-error-banner" class="hidden p-3 bg-red-600 text-white font-black text-xs uppercase tracking-wider text-center border-b-4 border-black dark:border-white">
        ⚠️ Sesi Anda telah berakhir. Silakan muat ulang halaman.
    </div>

    <!-- Messages Container -->
    <div id="chat-container" class="h-[380px] overflow-y-auto p-5 space-y-4 bg-white dark:bg-slate-900 flex flex-col">
        <div class="text-center text-black dark:text-white py-12 flex flex-col items-center justify-center gap-2">
            <div class="w-8 h-8 border-4 border-black dark:border-white border-t-yellow-400 dark:border-t-yellow-400 animate-spin bg-white dark:bg-slate-800 mb-2 shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]"></div>
            <span class="text-xs font-black uppercase tracking-wider">Memuat pesan...</span>
        </div>
    </div>

    <!-- Form Send Message / Closed Warning -->
    <div id="chat-input-container" class="p-4 border-t-4 border-black dark:border-white bg-white dark:bg-slate-950 flex gap-3 items-center {{ in_array($reservasi->status, ['pending', 'dp', 'lunas']) ? '' : 'hidden' }}">
        <input type="text" id="chat-input" placeholder="Tulis pesan Anda..." maxlength="1000" autocomplete="off"
            class="flex-1 px-4 py-2.5 border-4 border-black dark:border-white bg-white dark:bg-slate-900 text-sm font-bold text-black dark:text-white placeholder-gray-500 dark:placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-yellow-400 dark:focus:ring-yellow-400 transition duration-200 outline-none disabled:opacity-75 disabled:cursor-not-allowed">
        <button id="chat-send"
            class="bg-black dark:bg-yellow-400 hover:bg-yellow-400 dark:hover:bg-yellow-300 text-white dark:text-black hover:text-black font-black h-[48px] px-6 border-4 border-black dark:border-white shadow-[3px_3px_0px_0px_#000000] dark:shadow-[3px_3px_0px_0px_#ffffff] active:translate-x-[2px] active:translate-y-[2px] active:shadow-[1px_1px_0px_0px_#000000] dark:active:shadow-[1px_1px_0px_0px_#ffffff] transition duration-150 ease-in-out focus:outline-none flex items-center justify-center text-xs uppercase tracking-wider gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
            <span>Kirim</span>
            <span>✈️</span>
        </button>
    </div>

    <div id="chat-closed-container" class="p-4 border-t-4 border-black dark:border-white bg-red-100 dark:bg-slate-900 text-center text-xs font-black uppercase tracking-wider text-red-700 dark:text-red-400 {{ in_array($reservasi->status, ['pending', 'dp', 'lunas']) ? 'hidden' : '' }}">
        @if(($reservasi->status ?? '') === 'dikonfirmasi')
            🔒 Obrolan dinonaktifkan karena reservasi ini telah dikonfirmasi.
        @elseif(($reservasi->status ?? '') === 'batal')
            🔒 Obrolan dinonaktifkan karena reservasi ini telah dibatalkan.
        @else
            🔒 Obrolan ini telah ditutup secara permanen oleh Admin.
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatAppElement = document.getElementById('chat-box-app');
    if (!chatAppElement) return;

    // Baca data konfigurasi dari attributes HTML
    const RESERVASI_ID   = chatAppElement.getAttribute('data-reservasi-id');
    const USER_ROLE      = chatAppElement.getAttribute('data-user-role');
    const CHAT_FETCH_URL = chatAppElement.getAttribute('data-fetch-url');
    const CHAT_SEND_URL  = chatAppElement.getAttribute('data-send-url');
    const CSRF_TOKEN     = chatAppElement.getAttribute('data-csrf-token');

    let lastMessageId    = 0;
    let isPolling        = true;
    let pollTimeoutId    = null;
    let isSending        = false;
    let isFetching       = false; 
    let currentPollInterval = 4000;
    const MIN_POLL_INTERVAL = 4000;
    const MAX_POLL_INTERVAL = 20000;
    let emptyPollCount = 0;

    const errorBanner = document.getElementById('chat-error-banner');
    const container = document.getElementById('chat-container');
    const sendBtn = document.getElementById('chat-send');
    const inputField = document.getElementById('chat-input');
    const inputContainer = document.getElementById('chat-input-container');
    const closedContainer = document.getElementById('chat-closed-container');

    function showErrorBanner(message) {
        if (errorBanner) {
            errorBanner.textContent = message;
            errorBanner.classList.remove('hidden');
        }
    }

    function hideErrorBanner() {
        if (errorBanner) {
            errorBanner.classList.add('hidden');
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function renderMessage(msg) {
        const isMe = (USER_ROLE === 'admin') ? msg.is_admin : !msg.is_admin;
        const isClosedMsg = msg.message === '___CHAT_CLOSED___';
        const displayMessage = isClosedMsg ? '🔒 Chat ditutup oleh Admin' : escapeHtml(msg.message);

        const bubble = document.createElement('div');
        bubble.className = `flex ${isMe ? 'justify-end' : 'justify-start'} w-full mb-3`;
        bubble.setAttribute('data-id', msg.id);
        bubble.innerHTML = `<div class="max-w-[75%] px-4 py-3 border-2 border-black dark:border-white text-sm shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] transition-all duration-200 hover:shadow-[3px_3px_0px_0px_#000000] dark:hover:shadow-[3px_3px_0px_0px_#ffffff]
            ${isMe ? 'bg-yellow-400 text-black rounded-none' : 'bg-white dark:bg-slate-800 text-black dark:text-white rounded-none' }">
            <p class="font-black text-[10px] uppercase tracking-wider mb-1 ${isMe ? 'text-black opacity-80' : 'text-yellow-600 dark:text-yellow-400'}">${escapeHtml(msg.sender)}</p>
            <p class="break-words whitespace-pre-wrap font-bold leading-relaxed ${isMe ? 'text-black' : 'text-black dark:text-white'}">${displayMessage}</p>
            <p class="text-[9px] mt-1.5 text-right font-black opacity-75 ${isMe ? 'text-black' : 'text-black dark:text-white'}">${escapeHtml(msg.created_at)}</p>
        </div>`;
        return bubble;
    }

    async function pollMessages() {
        if (pollTimeoutId) {
            clearTimeout(pollTimeoutId);
            pollTimeoutId = null;
        }
        
        if (!isPolling || isFetching) return;
        if (document.visibilityState === 'hidden') {
            stopPolling();
            return;
        }

        isFetching = true; 
        try {
            const res  = await fetch(`${CHAT_FETCH_URL}?after=${lastMessageId}`, {
                headers: { 
                    'X-CSRF-TOKEN': CSRF_TOKEN, 
                    'Accept': 'application/json' 
                } 
            });

            if (!res.ok) {
                if (res.status === 401) {
                    stopPolling();
                    const hasReloaded = sessionStorage.getItem('chat_auth_reloaded');
                    if (!hasReloaded) {
                        sessionStorage.setItem('chat_auth_reloaded', 'true');
                        showErrorBanner("⚠️ Sesi Anda telah berakhir. Halaman akan dimuat ulang...");
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        showErrorBanner("⚠️ Sesi Anda telah berakhir. Silakan masuk kembali ke akun Anda.");
                    }
                    return;
                }
                if (res.status === 403) {
                    stopPolling();
                    showErrorBanner("⚠️ Akses obrolan ditolak atau telah ditutup.");
                    return;
                }
                if (res.status === 404) {
                    showErrorBanner("⚠️ Reservasi tidak ditemukan atau telah dihapus.");
                    stopPolling();
                    return;
                }
                throw new Error(`Server returned HTTP ${res.status}`);
            }

            sessionStorage.removeItem('chat_auth_reloaded');
            const data = await res.json();
            hideErrorBanner();

            if (data.messages && data.messages.length > 0) {
                if (lastMessageId === 0) {
                    container.innerHTML = '';
                }
                
                let appendedAny = false;
                data.messages.forEach(msg => { 
                    if (!container.querySelector(`[data-id="${msg.id}"]`)) {
                        container.appendChild(renderMessage(msg)); 
                        appendedAny = true;
                    }
                    lastMessageId = msg.id; 
                });
                
                if (appendedAny) {
                    container.scrollTop = container.scrollHeight;
                }

                currentPollInterval = MIN_POLL_INTERVAL;
                emptyPollCount = 0;
            } else {
                if (lastMessageId === 0) {
                    container.innerHTML = data.is_closed 
                        ? '<div class="text-center text-black dark:text-white py-12 text-xs italic font-bold uppercase tracking-wide">Obrolan ditutup.</div>'
                        : '<div class="text-center text-black dark:text-white py-12 text-xs italic font-bold uppercase tracking-wide">Belum ada obrolan. Mulai percakapan sekarang!</div>';
                }

                emptyPollCount++;
                if (emptyPollCount >= 3) {
                    currentPollInterval = Math.min(currentPollInterval + 2000, MAX_POLL_INTERVAL);
                }
            }

            if (data.is_closed) {
                stopPolling();
                if (inputContainer) inputContainer.classList.add('hidden');
                if (closedContainer) closedContainer.classList.remove('hidden');
                return;
            }
        } catch (e) { 
            console.error('Chat poll error:', e); 
            showErrorBanner("⚠️ Gangguan Koneksi. Mencoba menyambungkan kembali...");
            currentPollInterval = Math.min(currentPollInterval + 4000, MAX_POLL_INTERVAL);
        } finally {
            isFetching = false;
            if (isPolling) {
                pollTimeoutId = setTimeout(pollMessages, currentPollInterval);
            }
        }
    }

    function startPolling() {
        if (!isPolling) {
            isPolling = true;
            currentPollInterval = MIN_POLL_INTERVAL;
            emptyPollCount = 0;
            pollMessages();
        }
    }

    function stopPolling() {
        isPolling = false;
        if (pollTimeoutId) {
            clearTimeout(pollTimeoutId);
            pollTimeoutId = null;
        }
    }

    async function sendMessage() {
        if (isSending) return;
        const msg = inputField.value.trim();
        if (!msg) return;

        isSending = true;
        inputField.disabled = true;
        sendBtn.disabled = true;
        inputField.value = '';

        try {
            const response = await fetch(CHAT_SEND_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: msg })
            });

            if (response.status === 419) {
                showErrorBanner("⚠️ Sesi kedaluwarsa. Memuat ulang halaman...");
                setTimeout(() => window.location.reload(), 1500);
                return;
            }

            if (response.status === 403) {
                const data = await response.json();
                const errMsg = data.message || "Obrolan ini telah ditutup secara permanen oleh Admin.";
                showErrorBanner("⚠️ " + errMsg);
                stopPolling();
                
                if (inputContainer) inputContainer.classList.add('hidden');
                if (closedContainer) {
                    closedContainer.textContent = "🔒 " + errMsg;
                    closedContainer.classList.remove('hidden');
                }
                return;
            }

            if (!response.ok) throw new Error('Failed to send message');
            
            currentPollInterval = MIN_POLL_INTERVAL;
            emptyPollCount = 0;
            
            if (!isFetching) {
                await pollMessages();
            }
        } catch (error) {
            console.error('Gagal mengirim pesan:', error);
            showErrorBanner("⚠️ Pesan gagal dikirim. Silakan periksa koneksi internet Anda.");
            inputField.value = msg;
        } finally {
            isSending = false;
            inputField.disabled = false;
            sendBtn.disabled = false;
            inputField.focus();
        }
    }

    if (sendBtn) sendBtn.addEventListener('click', sendMessage);
    if (inputField) {
        inputField.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') sendMessage();
        });
    }

    // Deteksi status visibilitas untuk menghemat resource bandwidth
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') {
            stopPolling();
        } else {
            startPolling();
        }
    });

    window.addEventListener('focus', startPolling);
    window.addEventListener('blur', stopPolling);
    window.addEventListener('beforeunload', stopPolling);
    window.addEventListener('pagehide', stopPolling);

    // Jalankan polling pertama kali
    pollMessages();
});
</script>
