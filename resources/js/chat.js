import { marked } from 'marked';

(function () {
    if (window.aiChatScriptLoaded) return;
    window.aiChatScriptLoaded = true;

    let isFetching = false;

    document.addEventListener('DOMContentLoaded', function () {
        const chatStream = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');
        const newChatBtn = document.getElementById('new-chat-btn');
        const deleteChatBtn = document.getElementById('delete-chat-btn');
        const conversationTitle = document.getElementById('conversation-title');
        const csrfToken = document.querySelector('input[name="_token"]')?.value;

        marked.setOptions({ breaks: true, gfm: true });

        if (chatStream) {
            chatStream.scrollTop = chatStream.scrollHeight;
        }

        document.querySelectorAll('.chip-btn').forEach(button => {
            button.addEventListener('click', function () {
                if (isFetching || !userInput) return;
                userInput.value = this.getAttribute('data-prompt') || '';
                chatForm?.requestSubmit();
            });
        });

        chatForm?.addEventListener('submit', function (event) {
            event.preventDefault();

            if (isFetching) return;

            const text = userInput.value.trim();
            if (!text) return;

            appendMessage('user', text);
            userInput.value = '';
            submitChatMessage(text);
        });

        newChatBtn?.addEventListener('click', async function () {
            if (isFetching) return;

            setLoadingState(true);
            try {
                const response = await fetch(this.dataset.endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erreur serveur');

                window.location.href = data.url;
            } catch (error) {
                console.error(error);
                setLoadingState(false);
                alert('Impossible de créer une nouvelle conversation.');
            }
        });

        deleteChatBtn?.addEventListener('click', async function () {
            if (isFetching) return;
            if (!confirm('Voulez-vous vraiment supprimer cette conversation et tous ses messages ?')) return;

            setLoadingState(true);
            try {
                const response = await fetch(this.dataset.endpoint, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erreur serveur');

                window.location.href = data.redirect;
            } catch (error) {
                console.error(error);
                setLoadingState(false);
                alert('Impossible de supprimer la conversation.');
            }
        });

        async function submitChatMessage(messageText) {
            const endpoint = chatForm?.dataset.endpoint;
            if (!endpoint) return;

            setLoadingState(true);
            const loaderId = appendLoadingIndicator();

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: messageText }),
                });

                const data = await response.json();
                removeLoadingIndicator(loaderId);

                if (!response.ok) {
                    appendMessage('assistant', `⚠️ ${data.message || 'Erreur serveur'}`);
                    return;
                }

                appendMessage('assistant', data.content || 'Réponse reçue.');

                if (data.conversation?.title && conversationTitle) {
                    conversationTitle.textContent = data.conversation.title;
                }

                updateActiveConversationTitle(data.conversation?.title);
            } catch (error) {
                removeLoadingIndicator(loaderId);
                console.error(error);
                appendMessage('assistant', '⚠️ Impossible d\'interroger le service IA.');
            } finally {
                setLoadingState(false);
            }
        }

        function updateActiveConversationTitle(title) {
            if (!title) return;
            const active = document.querySelector('.conversation-item.bg-slate-800');
            const titleElement = active?.querySelector('.truncate');
            if (titleElement) titleElement.textContent = title;
        }

        function setLoadingState(loading) {
            isFetching = loading;
            if (sendBtn) sendBtn.disabled = loading;
            if (newChatBtn) newChatBtn.disabled = loading;
            if (deleteChatBtn) deleteChatBtn.disabled = loading;
            document.querySelectorAll('.chip-btn').forEach(btn => btn.disabled = loading);
        }

        function appendMessage(role, content) {
            document.getElementById('welcome-message')?.remove();

            const wrapper = document.createElement('div');
            wrapper.className = `flex gap-3 text-sm ${role === 'user' ? 'justify-end' : ''}`;

            if (role === 'user') {
                wrapper.innerHTML = `
                    <div class="bg-indigo-600 text-white rounded-2xl px-4 py-3 max-w-xl leading-relaxed shadow-xs">
                        ${escapeHtml(content)}
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">Vous</div>
                `;
            } else {
                const formattedContent = marked.parse(String(content));
                wrapper.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">IA</div>
                    <div class="ai-response bg-slate-800 text-slate-100 rounded-2xl px-5 py-3.5 max-w-2xl leading-relaxed border border-slate-700/60 shadow-xs">
                        ${formattedContent}
                    </div>
                `;
            }

            chatStream?.appendChild(wrapper);
            if (chatStream) chatStream.scrollTop = chatStream.scrollHeight;
        }

        function appendLoadingIndicator() {
            const id = 'loader-' + Date.now();
            const wrapper = document.createElement('div');
            wrapper.id = id;
            wrapper.className = 'flex gap-3 text-sm';
            wrapper.innerHTML = `
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">IA</div>
                <div class="bg-slate-800 text-slate-300 rounded-2xl px-4 py-3 border border-slate-700/60 shadow-xs flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce"></span>
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce [animation-delay:0.4s]"></span>
                </div>
            `;
            chatStream?.appendChild(wrapper);
            if (chatStream) chatStream.scrollTop = chatStream.scrollHeight;
            return id;
        }

        function removeLoadingIndicator(id) {
            document.getElementById(id)?.remove();
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    });
})();
