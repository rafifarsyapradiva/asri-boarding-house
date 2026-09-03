<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Obrolan Guest (Calon Penyewa)') }}
        </h2>
    </x-slot>

    <div class="py-12 text-black dark:text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Chat Threads List -->
                <div class="lg:col-span-1 flex flex-col">
                    <div class="admin-card flex-1 flex flex-col min-h-[500px]">
                        <div class="border-b-2 border-black dark:border-white pb-3 mb-4">
                            <h3 class="text-sm font-black uppercase tracking-wider">Daftar Pertanyaan Guest</h3>
                            <p class="admin-subtitle">Pengunjung publik yang menanyakan informasi kost.</p>
                        </div>

                        <!-- Threads List Scrollable Container -->
                        <div id="threads-container" class="flex-grow overflow-y-auto space-y-3 pr-1 max-h-[520px]">
                            <!-- Loadings/Threads render here via JS -->
                            <div class="text-center py-12 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                Memuat daftar chat...
                            </div>
                        </div>

                        <!-- Pagination Container -->
                        <div id="threads-pagination-container" class="mt-4 pt-3 border-t-2 border-black dark:border-white shrink-0"></div>
                    </div>
                </div>

                <!-- Right Column: Active Chat Box -->
                <div class="lg:col-span-2 flex flex-col">
                    <!-- State A: No Chat Selected -->
                    <div id="no-active-chat" class="admin-card flex-1 min-h-[500px] flex flex-col justify-center items-center text-center p-8 bg-slate-50 dark:bg-slate-900 border-4 border-dashed border-black dark:border-white">
                        <span class="text-4xl mb-3">💬</span>
                        <h4 class="font-black uppercase tracking-wider text-xs sm:text-sm">Pilih Obrolan</h4>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-1 max-w-xs leading-relaxed">
                            Silakan klik salah satu nama guest di panel sebelah kiri untuk memulai sesi tanya jawab secara langsung.
                        </p>
                    </div>

                    <!-- State B: Active Chat Window -->
                    <div id="active-chat-panel" class="admin-card flex-grow min-h-[500px] flex flex-col hidden">
                        <!-- Chat Header -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b-4 border-black dark:border-white pb-4 mb-4 shrink-0">
                            <div>
                                <h3 id="chat-header-name" class="text-sm sm:text-base font-black uppercase tracking-wider">-</h3>
                                <p id="chat-header-phone" class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-0.5">-</p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <a id="chat-wa-link" href="#" target="_blank" rel="noopener noreferrer"
                                   class="inline-block px-2.5 py-1.5 bg-green-500 hover:bg-green-600 text-white border-2 border-black dark:border-white font-black text-[10px] uppercase tracking-wider shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#000000] dark:hover:shadow-[1px_1px_0px_0px_#ffffff] transition">
                                    📲 Hubungi WA
                                </a>
                                <button type="button" id="close-thread-btn" 
                                        class="px-2.5 py-1.5 bg-yellow-400 hover:bg-yellow-300 text-black border-2 border-black dark:border-white font-black text-[10px] uppercase tracking-wider shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#000000] dark:hover:shadow-[1px_1px_0px_0px_#ffffff] transition cursor-pointer">
                                    🔒 Tandai Selesai
                                </button>
                                <button type="button" id="delete-thread-btn" 
                                        class="px-2.5 py-1.5 bg-red-500 hover:bg-red-600 text-white border-2 border-black dark:border-white font-black text-[10px] uppercase tracking-wider shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#000000] dark:hover:shadow-[1px_1px_0px_0px_#ffffff] transition cursor-pointer">
                                    🗑️ Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Chat Messages Container -->
                        <div id="chat-messages-container" class="flex-grow overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white min-h-[300px] max-h-[360px] flex flex-col">
                            <!-- Messages render dynamically -->
                        </div>

                        <!-- Alert Closed Thread -->
                        <div id="chat-closed-alert" class="hidden bg-red-100 dark:bg-slate-800 text-red-600 dark:text-red-400 border-4 border-black dark:border-white p-2.5 text-center text-xs font-black uppercase tracking-wider my-3 shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]">
                            🔒 Obrolan ini telah ditutup secara permanen.
                        </div>

                        <!-- Admin Input Form -->
                        <div id="admin-chat-input-container" class="mt-4 flex gap-3 items-center shrink-0">
                            <input type="text" id="admin-message-input" placeholder="Tulis balasan pesan Anda..." autocomplete="off" maxlength="1000"
                                   class="flex-grow px-3 py-2.5 border-4 border-black bg-white dark:bg-slate-950 text-xs sm:text-sm font-bold text-black dark:text-white placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-yellow-400">
                            <button id="admin-send-btn"
                                    class="bg-black dark:bg-yellow-400 hover:bg-yellow-400 dark:hover:bg-yellow-400 text-white dark:text-black font-black h-[48px] px-5 border-4 border-black dark:border-white shadow-[3px_3px_0px_0px_#000000] dark:shadow-[3px_3px_0px_0px_#ffffff] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[2px_2px_0px_0px_#000000] dark:active:shadow-[2px_2px_0px_0px_#ffffff] transition text-xs uppercase tracking-wider cursor-pointer">
                                Kirim ✈️
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modularized JavaScript Engine -->
    <script>
    (function () {
        'use strict';

        // DOM Elements
        const threadsContainer = document.getElementById('threads-container');
        const paginationContainer = document.getElementById('threads-pagination-container');
        const noActiveChat = document.getElementById('no-active-chat');
        const activeChatPanel = document.getElementById('active-chat-panel');
        
        const chatHeaderName = document.getElementById('chat-header-name');
        const chatHeaderPhone = document.getElementById('chat-header-phone');
        const chatWaLink = document.getElementById('chat-wa-link');
        const closeThreadBtn = document.getElementById('close-thread-btn');
        const deleteThreadBtn = document.getElementById('delete-thread-btn');
        
        const msgContainer = document.getElementById('chat-messages-container');
        const adminMsgInput = document.getElementById('admin-message-input');
        const adminSendBtn = document.getElementById('admin-send-btn');
        const adminInputContainer = document.getElementById('admin-chat-input-container');
        const closedAlert = document.getElementById('chat-closed-alert');

        // Application State
        let activeThreadId = null;
        let lastMsgId = 0;
        let msgPollTimeout = null;
        let isSending = false;
        let currentThreadsPage = 1;
        let pollGeneration = 0;
        let currentPollInterval = 4000;
        const MIN_POLL_INTERVAL = 4000;
        const MAX_POLL_INTERVAL = 8000;
        let emptyPollCount = 0;

        /**
         * Helper: Escape HTML special characters to prevent XSS
         */
        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, (m) => map[m]);
        }

        /**
         * Fetch thread list from server
         */
        async function fetchThreads(page = 1) {
            currentThreadsPage = page;
            try {
                const res = await fetch(`{{ route("admin.guest-chats.threads") }}?page=${page}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);
                
                const data = await res.json();
                renderThreads(data.threads.data);
                renderPagination(data.threads);
            } catch (e) {
                console.error('Error fetching threads:', e);
            }
        }

        /**
         * Render threads to side panel
         */
        function renderThreads(threads) {
            if (!threads || threads.length === 0) {
                threadsContainer.innerHTML = '<div class="text-center py-12 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Tidak ada obrolan guest.</div>';
                return;
            }

            threadsContainer.innerHTML = '';
            threads.forEach(t => {
                const isSelected = activeThreadId === t.id;
                const card = document.createElement('div');
                card.className = `p-3.5 border-4 border-black dark:border-white shadow-[3px_3px_0px_0px_#000000] dark:shadow-[3px_3px_0px_0px_#ffffff] transition cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 relative
                    ${isSelected ? 'bg-yellow-400 dark:bg-yellow-400 text-black dark:text-black font-extrabold shadow-[1px_1px_0px_0px_#000000] translate-x-[2px] translate-y-[2px]' : 'bg-white dark:bg-slate-900 text-black dark:text-white'}`;
                
                card.addEventListener('click', () => selectThread(t));

                const unreadBadge = t.unread_count > 0 
                    ? `<span class="absolute top-2 right-2 px-1.5 py-0.5 bg-red-500 text-white font-black text-[9px] border border-black shadow-[1px_1px_0px_0px_#000000] rounded-none">${t.unread_count}</span>` 
                    : '';

                const statusSymbol = t.status === 'closed' ? '🔒' : '⚡';

                // Robust XSS escaping on all dynamic properties
                card.innerHTML = `
                    <div class="pr-6">
                        <div class="flex justify-between items-center mb-1">
                            <h4 class="text-xs font-black uppercase tracking-wider truncate max-w-[150px]">${escapeHtml(t.name)}</h4>
                            <span class="text-[9px] font-black opacity-60">${escapeHtml(t.updated_at)}</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 truncate mb-1.5">${escapeHtml(t.no_hp)}</p>
                        <p class="text-[10px] truncate ${isSelected ? 'text-black opacity-90' : 'text-slate-400 dark:text-slate-300'}">
                            <span class="mr-0.5 text-xs">${statusSymbol}</span> ${escapeHtml(t.last_message)}
                        </p>
                    </div>
                    ${unreadBadge}
                `;
                threadsContainer.appendChild(card);
            });
        }

        /**
         * Render pagination with Event Delegation
         */
        function renderPagination(paginator) {
            if (!paginationContainer) return;
            if (paginator.last_page <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            paginationContainer.innerHTML = `
                <div class="flex items-center justify-between gap-2">
                    ${paginator.current_page > 1 ? `
                        <button data-page="${paginator.current_page - 1}" class="js-page-btn px-2 py-1 bg-white dark:bg-slate-800 text-black dark:text-white border-2 border-black dark:border-white font-black text-[10px] uppercase shadow-[1.5px_1.5px_0px_0px_#000000] dark:shadow-[1.5px_1.5px_0px_0px_#ffffff] hover:bg-[#FACC15] hover:text-black transition cursor-pointer">
                            ◀
                        </button>
                    ` : `
                        <span class="px-2 py-1 bg-gray-100 dark:bg-slate-900 text-gray-400 dark:text-gray-600 border-2 border-black dark:border-slate-700 font-black text-[10px] uppercase cursor-not-allowed select-none">◀</span>
                    `}

                    <span class="text-[10px] font-black uppercase tracking-wider text-black dark:text-white">
                        Hal ${paginator.current_page} / ${paginator.last_page}
                    </span>

                    ${paginator.current_page < paginator.last_page ? `
                        <button data-page="${paginator.current_page + 1}" class="js-page-btn px-2 py-1 bg-white dark:bg-slate-800 text-black dark:text-white border-2 border-black dark:border-white font-black text-[10px] uppercase shadow-[1.5px_1.5px_0px_0px_#000000] dark:shadow-[1.5px_1.5px_0px_0px_#ffffff] hover:bg-[#FACC15] hover:text-black transition cursor-pointer">
                            ▶
                        </button>
                    ` : `
                        <span class="px-2 py-1 bg-gray-100 dark:bg-slate-900 text-gray-400 dark:text-gray-600 border-2 border-black dark:border-slate-700 font-black text-[10px] uppercase cursor-not-allowed select-none">▶</span>
                    `}
                </div>
            `;
        }

        // Handle pagination click via Delegation
        if (paginationContainer) {
            paginationContainer.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-page-btn');
                if (btn && btn.dataset.page) {
                    fetchThreads(parseInt(btn.dataset.page, 10));
                }
            });
        }

        /**
         * Select and open thread
         */
        function selectThread(thread) {
            pollGeneration++;
            if (msgPollTimeout) {
                clearTimeout(msgPollTimeout);
                msgPollTimeout = null;
            }
            activeThreadId = thread.id;
            lastMsgId = 0;
            currentPollInterval = MIN_POLL_INTERVAL;
            emptyPollCount = 0;
            msgContainer.innerHTML = '<div class="text-center py-12 text-xs font-bold text-slate-400 uppercase tracking-widest">Memuat pesan...</div>';
            
            noActiveChat.classList.add('hidden');
            activeChatPanel.classList.remove('hidden');
            
            chatHeaderName.textContent = thread.name;
            chatHeaderPhone.textContent = thread.no_hp;
            
            let waNumber = (thread.no_hp || '').replace(/[^0-9]/g, '');
            if (waNumber.startsWith('0')) {
                waNumber = '62' + waNumber.substring(1);
            }
            chatWaLink.href = `https://wa.me/${waNumber}?text=Halo%20Kak%20${encodeURIComponent(thread.name)},%20saya%20Admin%20Asri%20Boarding%20House.%20Ada%20yang%20bisa%20kami%20bantu%20terkait%20pertanyaan%20Kakak%20sebelumnya%3F%20Terima%20kasih.`;
            
            if (thread.status === 'closed') {
                closedAlert.classList.remove('hidden');
                closeThreadBtn.classList.add('hidden');
                if (adminInputContainer) adminInputContainer.classList.add('hidden');
            } else {
                closedAlert.classList.add('hidden');
                closeThreadBtn.classList.remove('hidden');
                if (adminInputContainer) adminInputContainer.classList.remove('hidden');
            }

            fetchThreads(currentThreadsPage);
            pollMessages();
        }

        /**
         * Message Polling Engine with exponential backoff
         */
        async function pollMessages() {
            if (!activeThreadId) return;
            const threadIdAtRequest = activeThreadId;
            const generationAtRequest = pollGeneration;

            if (document.visibilityState === 'hidden') {
                msgPollTimeout = setTimeout(pollMessages, MIN_POLL_INTERVAL);
                return;
            }

            try {
                const res = await fetch(`/admin/guest-chats/${threadIdAtRequest}/messages?after=${lastMsgId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const data = await res.json();
                
                // Discard stale responses from previous thread switches
                if (pollGeneration !== generationAtRequest || activeThreadId !== threadIdAtRequest) return;
                
                if (data.messages && data.messages.length > 0) {
                    if (lastMsgId === 0) msgContainer.innerHTML = '';

                    data.messages.forEach(msg => {
                        if (msg.message === '___CHAT_CLOSED___') {
                            const sysMsg = document.createElement('div');
                            sysMsg.className = 'w-full text-center py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest my-1 border-t border-b border-dashed border-slate-300 dark:border-slate-700';
                            sysMsg.textContent = '🔒 Chat ditutup oleh Admin';
                            msgContainer.appendChild(sysMsg);
                        } else {
                            msgContainer.appendChild(renderMessage(msg));
                        }
                        lastMsgId = msg.id;
                    });
                    msgContainer.scrollTop = msgContainer.scrollHeight;

                    currentPollInterval = MIN_POLL_INTERVAL;
                    emptyPollCount = 0;
                } else {
                    if (lastMsgId === 0) {
                        msgContainer.innerHTML = '<div class="text-center py-12 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest italic">Belum ada obrolan. Balas di bawah untuk menyapa.</div>';
                    }

                    emptyPollCount++;
                    if (emptyPollCount >= 3) {
                        currentPollInterval = Math.min(currentPollInterval + 2000, MAX_POLL_INTERVAL);
                    }
                }

                if (data.status === 'closed') {
                    closedAlert.classList.remove('hidden');
                    closeThreadBtn.classList.add('hidden');
                    if (adminInputContainer) adminInputContainer.classList.add('hidden');
                }

            } catch (e) {
                console.error('Error polling messages:', e);
                currentPollInterval = Math.min(currentPollInterval + 4000, MAX_POLL_INTERVAL);
            } finally {
                if (pollGeneration === generationAtRequest && activeThreadId === threadIdAtRequest) {
                    msgPollTimeout = setTimeout(pollMessages, currentPollInterval);
                }
            }
        }

        /**
         * Render Chat Message Bubble
         */
        function renderMessage(msg) {
            const isAdmin = msg.sender_type === 'admin';
            const bubble = document.createElement('div');
            bubble.className = `flex ${isAdmin ? 'justify-end' : 'justify-start'} w-full mb-1`;
            
            const bubbleClass = isAdmin 
                ? 'bg-yellow-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' 
                : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]';

            bubble.innerHTML = `
                <div class="max-w-[75%] px-3.5 py-2.5 text-xs font-bold leading-normal ${bubbleClass}">
                    <p class="text-[9px] uppercase font-black opacity-70 mb-1 ${isAdmin ? 'text-black' : 'text-yellow-600'}">
                        ${escapeHtml(msg.sender_name)}
                    </p>
                    <p class="break-words font-medium leading-relaxed whitespace-pre-wrap">${escapeHtml(msg.message)}</p>
                    <p class="text-[8px] text-right font-black opacity-60 mt-1">${escapeHtml(msg.created_at)}</p>
                </div>
            `;
            return bubble;
        }

        /**
         * Send message handler
         */
        async function sendMessage() {
            if (isSending || !activeThreadId) return;
            const msg = adminMsgInput.value.trim();
            if (!msg) return;

            isSending = true;
            adminMsgInput.disabled = true;
            adminSendBtn.disabled = true;

            try {
                const response = await fetch(`/admin/guest-chats/${activeThreadId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: msg })
                });

                const data = await response.json();
                if (data.success) {
                    adminMsgInput.value = '';
                    pollGeneration++;
                    currentPollInterval = MIN_POLL_INTERVAL;
                    emptyPollCount = 0;
                    if (msgPollTimeout) clearTimeout(msgPollTimeout);
                    await pollMessages();
                    fetchThreads(currentThreadsPage);
                } else {
                    if (window.showToast) window.showToast(data.message || 'Gagal mengirim pesan.', 'error');
                }
            } catch (e) {
                console.error(e);
                if (window.showToast) window.showToast('Gagal mengirim pesan. Coba lagi.', 'error');
            } finally {
                isSending = false;
                adminMsgInput.disabled = false;
                adminSendBtn.disabled = false;
                adminMsgInput.focus();
            }
        }

        // Close thread listener
        closeThreadBtn.addEventListener('click', function () {
            if (!activeThreadId) return;
            
            const doClose = async () => {
                try {
                    const response = await fetch(`/admin/guest-chats/${activeThreadId}/close`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        pollGeneration++;
                        currentPollInterval = MIN_POLL_INTERVAL;
                        emptyPollCount = 0;
                        if (msgPollTimeout) clearTimeout(msgPollTimeout);
                        await pollMessages();
                        fetchThreads(currentThreadsPage);
                        if (window.showToast) window.showToast('Obrolan berhasil ditandai selesai.', 'success');
                    }
                } catch (e) {
                    console.error(e);
                    if (window.showToast) window.showToast('Gagal menutup chat.', 'error');
                }
            };

            if (window.brutalistConfirm) {
                window.brutalistConfirm('Tandai Selesai', 'Tandai obrolan ini sebagai selesai/closed?', doClose, { isDanger: false });
            } else {
                doClose();
            }
        });

        // Delete thread listener
        deleteThreadBtn.addEventListener('click', function () {
            if (!activeThreadId) return;

            const doDelete = async () => {
                try {
                    const response = await fetch(`/admin/guest-chats/${activeThreadId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        pollGeneration++;
                        activeThreadId = null;
                        if (msgPollTimeout) clearTimeout(msgPollTimeout);
                        msgPollTimeout = null;
                        
                        noActiveChat.classList.remove('hidden');
                        activeChatPanel.classList.add('hidden');
                        
                        fetchThreads(currentThreadsPage);
                        if (window.showToast) window.showToast('Obrolan berhasil dihapus.', 'success');
                    }
                } catch (e) {
                    console.error(e);
                    if (window.showToast) window.showToast('Gagal menghapus chat.', 'error');
                }
            };

            if (window.brutalistConfirm) {
                window.brutalistConfirm('Hapus Obrolan', 'Apakah Anda yakin ingin menghapus obrolan ini secara permanen?', doDelete, { isDanger: true });
            } else {
                doDelete();
            }
        });

        // Event Listeners for Input & Sending
        adminSendBtn.addEventListener('click', sendMessage);
        adminMsgInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') sendMessage();
        });

        adminMsgInput.addEventListener('input', function () {
            if (currentPollInterval > MIN_POLL_INTERVAL) {
                currentPollInterval = MIN_POLL_INTERVAL;
                emptyPollCount = 0;
                if (msgPollTimeout) {
                    clearTimeout(msgPollTimeout);
                    msgPollTimeout = setTimeout(pollMessages, currentPollInterval);
                }
            }
        });

        // Smart background polling & page lifecycle protection
        document.addEventListener('visibilitychange', function () {
            if (!activeThreadId) return;
            if (document.visibilityState === 'hidden') {
                pollGeneration++;
                if (msgPollTimeout) {
                    clearTimeout(msgPollTimeout);
                    msgPollTimeout = null;
                }
            } else if (document.visibilityState === 'visible') {
                pollGeneration++;
                currentPollInterval = MIN_POLL_INTERVAL;
                emptyPollCount = 0;
                pollMessages();
            }
        });

        // Initial Load
        document.addEventListener('DOMContentLoaded', function () {
            fetchThreads(1);
            
            // Background thread list refresh (only when tab visible)
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    fetchThreads(currentThreadsPage);
                }
            }, 10000);
        });

    })();
    </script>
</x-app-layout>
