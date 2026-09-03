/**
 * Guest Chat Widget Frontend Controller
 */
document.addEventListener('DOMContentLoaded', function () {
    const widget = document.getElementById('guest-chat-widget');
    if (!widget) return;

    const routes = {
        start: widget.dataset.startRoute,
        messages: widget.dataset.messagesRoute,
        send: widget.dataset.sendRoute,
        csrf: widget.dataset.csrfToken
    };

    const toggleBtn = document.getElementById('guest-chat-toggle');
    const closeBtn = document.getElementById('guest-chat-close');
    const chatWindow = document.getElementById('guest-chat-window');
    
    const formState = document.getElementById('guest-state-form');
    const chatState = document.getElementById('guest-state-chat');
    
    const startBtn = document.getElementById('guest-start-btn');
    const sendBtn = document.getElementById('guest-send-btn');
    
    const guestNameInput = document.getElementById('guest-name');
    const guestPhoneInput = document.getElementById('guest-phone');
    const guestMsgInput = document.getElementById('guest-message-input');
    const msgContainer = document.getElementById('guest-messages-container');
    const formError = document.getElementById('form-error');
    const closedBanner = document.getElementById('guest-closed-banner');

    let lastMsgId = 0;
    let pollTimeout = null;
    let isPolling = false;
    let isSending = false;
    let currentPollInterval = 4000;
    const MIN_POLL_INTERVAL = 4000;
    const MAX_POLL_INTERVAL = 8000;
    let emptyPollCount = 0;

    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    let cookieToken = getCookie('guest_chat_token');
    let storageToken = localStorage.getItem('guest_chat_token');
    let sessionToken = cookieToken || storageToken || null;

    if (sessionToken) {
        if (!storageToken) localStorage.setItem('guest_chat_token', sessionToken);
        if (!cookieToken) document.cookie = `guest_chat_token=${sessionToken}; path=/; max-age=${60 * 60 * 24 * 30}; SameSite=Lax`;
    } else {
        localStorage.removeItem('guest_chat_token');
        document.cookie = "guest_chat_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    toggleBtn?.addEventListener('click', function () {
        const isOpen = !chatWindow.classList.contains('pointer-events-none');
        if (isOpen) {
            closeWindow();
        } else {
            openWindow();
        }
    });

    closeBtn?.addEventListener('click', closeWindow);

    function openWindow() {
        chatWindow.classList.remove('pointer-events-none', 'translate-y-10', 'opacity-0');
        chatWindow.classList.add('translate-y-0', 'opacity-100');
        const icon = document.getElementById('toggle-icon');
        if (icon) icon.innerText = '✕';
        
        if (window.innerWidth < 1024) {
            document.body.classList.add('overflow-hidden');
        }
        
        if (sessionToken) {
            showChatState();
        } else {
            showFormState();
        }
    }

    function closeWindow() {
        chatWindow.classList.add('pointer-events-none', 'translate-y-10', 'opacity-0');
        chatWindow.classList.remove('translate-y-0', 'opacity-100');
        const icon = document.getElementById('toggle-icon');
        if (icon) icon.innerText = '💬';
        
        document.body.classList.remove('overflow-hidden');
        stopPolling();
    }

    function showFormState() {
        formState?.classList.remove('hidden');
        chatState?.classList.add('hidden');
    }

    function showChatState() {
        formState?.classList.add('hidden');
        chatState?.classList.remove('hidden');
        startPolling();
        setTimeout(() => {
            if (msgContainer) msgContainer.scrollTop = msgContainer.scrollHeight;
        }, 50);
    }

    startBtn?.addEventListener('click', async function () {
        const name = guestNameInput.value.trim();
        let phone = guestPhoneInput.value.trim();
        phone = phone.replace(/[\s\-\(\)]/g, '');

        if (!name || !phone) {
            showError('Nama dan Nomor WhatsApp wajib diisi.');
            return;
        }

        const phoneRegex = /^(08|628|\+628)[0-9]{8,13}$/;
        if (!phoneRegex.test(phone)) {
            showError('Format nomor WhatsApp tidak valid.');
            return;
        }

        if (formError) formError.classList.add('hidden');
        startBtn.disabled = true;
        startBtn.innerText = 'Menghubungkan...';

        try {
            const response = await fetch(routes.start, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': routes.csrf
                },
                body: JSON.stringify({ name: name, no_hp: phone })
            });

            const data = await response.json();
            if (data.success) {
                sessionToken = data.token;
                localStorage.setItem('guest_chat_token', sessionToken);
                document.cookie = `guest_chat_token=${sessionToken}; path=/; max-age=${60 * 60 * 24 * 30}`;
                showChatState();
            } else {
                showError(data.message || 'Gagal memulai chat.');
            }
        } catch (e) {
            console.error(e);
            showError('Terjadi kesalahan koneksi.');
        } finally {
            startBtn.disabled = false;
            startBtn.innerText = 'Mulai Chat 🚀';
        }
    });

    function showError(msg) {
        if (!formError) return;
        formError.innerText = '⚠️ ' + msg;
        formError.classList.remove('hidden');
    }

    function renderMessage(msg) {
        const isMe = msg.sender_type === 'guest';
        
        const bubble = document.createElement('div');
        bubble.className = `flex ${isMe ? 'justify-end' : 'justify-start'} w-full mb-1`;
        
        const bubbleClass = isMe 
            ? 'bg-yellow-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_#000000]' 
            : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_0px_#000000]';

        const card = document.createElement('div');
        card.className = `max-w-[80%] px-3 py-2 border-black text-xs font-bold leading-normal ${bubbleClass}`;

        const sender = document.createElement('p');
        sender.className = `text-[9px] uppercase font-black opacity-75 mb-0.5 ${isMe ? 'text-black' : 'text-yellow-600'}`;
        sender.textContent = msg.sender_name;

        const content = document.createElement('p');
        content.className = 'break-words font-medium whitespace-pre-wrap';
        content.textContent = msg.message;

        const time = document.createElement('p');
        time.className = 'text-[8px] text-right font-black opacity-60 mt-1';
        
        let parsedTime = '';
        if (msg.created_at) {
            try {
                const date = new Date(msg.created_at.replace(/-/g, '/'));
                parsedTime = !isNaN(date.getTime())
                    ? date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false })
                    : msg.created_at.substring(11, 16);
            } catch (e) {
                parsedTime = msg.created_at.substring(11, 16) || '--:--';
            }
        }
        time.textContent = parsedTime;

        card.appendChild(sender);
        card.appendChild(content);
        card.appendChild(time);
        bubble.appendChild(card);

        return bubble;
    }

    async function pollMessages() {
        if (!sessionToken || !isPolling) return;

        if (document.visibilityState === 'hidden') {
            pollTimeout = setTimeout(pollMessages, MIN_POLL_INTERVAL);
            return;
        }

        try {
            const res = await fetch(`${routes.messages}?after=${lastMsgId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Guest-Chat-Token': sessionToken
                }
            });

            if (res.status === 404) {
                localStorage.removeItem('guest_chat_token');
                document.cookie = "guest_chat_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                sessionToken = null;
                showFormState();
                return;
            }

            const data = await res.json();
            
            if (data.messages.length > 0) {
                if (lastMsgId === 0 && msgContainer) {
                    msgContainer.innerHTML = '';
                }

                data.messages.forEach(msg => {
                    if (msg.message === '___CHAT_CLOSED___') {
                        const sysMsg = document.createElement('div');
                        sysMsg.className = 'w-full text-center py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest my-1 border-t border-b border-dashed border-slate-300';
                        sysMsg.innerText = '🔒 Chat ditutup oleh Admin';
                        msgContainer?.appendChild(sysMsg);
                    } else {
                        msgContainer?.appendChild(renderMessage(msg));
                    }
                    lastMsgId = msg.id;
                });
                if (msgContainer) msgContainer.scrollTop = msgContainer.scrollHeight;

                currentPollInterval = MIN_POLL_INTERVAL;
                emptyPollCount = 0;
            } else {
                if (lastMsgId === 0 && msgContainer) {
                    if (data.status === 'closed') {
                        msgContainer.innerHTML = '<div class="text-center py-12 text-slate-400 text-xs font-black uppercase tracking-widest">Obrolan ditutup</div>';
                    } else {
                        renderQuickQuestions();
                    }
                }

                emptyPollCount++;
                if (emptyPollCount >= 3) {
                    currentPollInterval = Math.min(currentPollInterval + 2000, MAX_POLL_INTERVAL);
                }
            }

            const closedContainer = document.getElementById('guest-closed-container');
            const inputContainer = document.getElementById('guest-chat-input-container');
            if (data.status === 'closed') {
                isPolling = false;
                if (pollTimeout) {
                    clearTimeout(pollTimeout);
                    pollTimeout = null;
                }
                inputContainer?.classList.add('hidden');
                closedContainer?.classList.remove('hidden');
                closedBanner?.classList.add('hidden');
                return;
            } else {
                inputContainer?.classList.remove('hidden');
                closedContainer?.classList.add('hidden');
                closedBanner?.classList.add('hidden');
            }

        } catch (e) {
            console.error('Guest Chat polling error:', e);
            currentPollInterval = Math.min(currentPollInterval + 4000, MAX_POLL_INTERVAL);
        } finally {
            if (isPolling) {
                pollTimeout = setTimeout(pollMessages, currentPollInterval);
            }
        }
    }

    function renderQuickQuestions() {
        if (!msgContainer) return;
        const questions = [
            '🔑 Apakah kamar VIP lantai 1 masih tersedia?',
            '💰 Bagaimana skema pembayaran DP 30%?',
            '🔌 Apakah tipe standar sudah termasuk free listrik?',
            '🛏️ Berapa ukuran kasur tipe Deluxe?',
            '📍 Apakah boleh survei lokasi kost langsung?'
        ];

        let html = `
            <div class="text-center py-6">
                <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-4 px-4 leading-relaxed">
                    Halo! Silakan tulis pesan Anda atau pilih salah satu pertanyaan populer berikut:
                </p>
                <div class="flex flex-col gap-2.5 px-4 items-center" id="quick-questions-wrapper">
        `;

        questions.forEach(q => {
            html += `
                <button type="button" data-question="${q.replace(/^.+?\s/, '')}"
                        class="quick-q-btn w-full text-left bg-white hover:bg-yellow-400 text-black border-2 border-black font-bold py-1.5 px-3 text-[10px] shadow-[2px_2px_0px_0px_#000000] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#000000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition cursor-pointer">
                    ${q}
                </button>
            `;
        });

        html += `</div></div>`;
        msgContainer.innerHTML = html;

        document.getElementById('quick-questions-wrapper')?.addEventListener('click', function(e) {
            const btn = e.target.closest('.quick-q-btn');
            if (btn && !btn.disabled) {
                const questionText = btn.dataset.question;
                if (questionText) {
                    document.querySelectorAll('.quick-q-btn').forEach(b => b.disabled = true);
                    if (guestMsgInput) guestMsgInput.value = questionText;
                    sendMessage();
                }
            }
        });
    }

    function startPolling() {
        if (isPolling) return;
        isPolling = true;
        if (msgContainer && msgContainer.innerHTML.trim() === '') {
            msgContainer.innerHTML = '<div class="text-center text-slate-400 text-xs py-8">Memuat obrolan...</div>';
        }
        pollMessages();
    }

    function stopPolling() {
        isPolling = false;
        if (pollTimeout) {
            clearTimeout(pollTimeout);
            pollTimeout = null;
        }
    }

    async function sendMessage() {
        if (isSending) return;
        const msg = guestMsgInput?.value.trim();
        if (!msg) return;

        isSending = true;
        if (guestMsgInput) guestMsgInput.disabled = true;
        if (sendBtn) sendBtn.disabled = true;
        if (guestMsgInput) guestMsgInput.value = '';

        try {
            const res = await fetch(routes.send, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Guest-Chat-Token': sessionToken,
                    'X-CSRF-TOKEN': routes.csrf
                },
                body: JSON.stringify({ message: msg })
            });

            const data = await res.json();
            if (data.success) {
                currentPollInterval = MIN_POLL_INTERVAL;
                emptyPollCount = 0;
                stopPolling();
                isPolling = true;
                pollMessages();
            } else {
                showError(data.message || 'Gagal mengirim pesan.');
                if (guestMsgInput) guestMsgInput.value = msg;
            }
        } catch (e) {
            console.error(e);
            showError('Kesalahan jaringan. Gagal mengirim pesan.');
            if (guestMsgInput) guestMsgInput.value = msg;
        } finally {
            isSending = false;
            if (guestMsgInput) guestMsgInput.disabled = false;
            if (sendBtn) sendBtn.disabled = false;
            guestMsgInput?.focus();
        }
    }

    sendBtn?.addEventListener('click', sendMessage);
    guestMsgInput?.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    guestMsgInput?.addEventListener('input', function () {
        if (currentPollInterval > MIN_POLL_INTERVAL) {
            currentPollInterval = MIN_POLL_INTERVAL;
            emptyPollCount = 0;
            if (pollTimeout) {
                clearTimeout(pollTimeout);
                pollTimeout = setTimeout(pollMessages, currentPollInterval);
            }
        }
    });

    document.addEventListener('visibilitychange', function () {
        if (!sessionToken) return;
        const isWindowOpen = !chatWindow.classList.contains('pointer-events-none');
        if (isWindowOpen) {
            if (document.visibilityState === 'hidden') {
                stopPolling();
            } else if (document.visibilityState === 'visible') {
                currentPollInterval = MIN_POLL_INTERVAL;
                emptyPollCount = 0;
                startPolling();
            }
        }
    });

    window.addEventListener('focus', function () {
        const isWindowOpen = !chatWindow.classList.contains('pointer-events-none');
        if (isWindowOpen && sessionToken && !isPolling) {
            currentPollInterval = MIN_POLL_INTERVAL;
            emptyPollCount = 0;
            startPolling();
        }
    });

    window.addEventListener('blur', function () {
        stopPolling();
    });
});
