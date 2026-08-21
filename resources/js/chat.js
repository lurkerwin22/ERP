(function () {
    if (window.aiChatScriptLoaded) return;
    window.aiChatScriptLoaded = true;

    let isFetching = false;

    document.addEventListener('DOMContentLoaded', function () {
        const chatStream = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');
        const csrfToken = document.querySelector('input[name="_token"]')?.value;

        if (chatStream) chatStream.scrollTop = chatStream.scrollHeight;

        if (typeof marked !== 'undefined') {
            marked.setOptions({ breaks: true, gfm: true });
        }

        // Delegated click handler to prevent duplicate event bindings
        document.addEventListener('click', function (e) {
            const chip = e.target.closest('.chip-btn');
            if (!chip || isFetching) return;

            e.preventDefault();
            e.stopImmediatePropagation();

            const promptText = chip.getAttribute('data-prompt');
            const endpoint = chip.getAttribute('data-endpoint');

            if (endpoint) {
                appendMessage('user', promptText);
                fetchReport(endpoint);
            } else if (userInput) {
                userInput.value = promptText;
            }
        });

        // Handle Manual Form Submit
        chatForm?.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            if (isFetching) return;

            const text = userInput.value.trim();
            if (!text) return;

            appendMessage('user', text);
            userInput.value = '';
            submitChatMessage(text);
        });

        async function fetchReport(endpoint) {
            setLoadingState(true);
            const loaderId = appendLoadingIndicator();
            try {
                const response = await fetch(endpoint, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const reportData = await response.json();
                removeLoadingIndicator(loaderId);
                appendMessage('assistant', formatReportHtml(reportData), true);
            } catch (err) {
                removeLoadingIndicator(loaderId);
                appendMessage('assistant', '⚠️ Erreur lors de la récupération du rapport.');
            } finally {
                setLoadingState(false);
            }
        }

        async function submitChatMessage(messageText) {
            const endpoint = chatForm.getAttribute('data-endpoint');
            setLoadingState(true);
            const loaderId = appendLoadingIndicator();

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        messages: [{ role: 'user', content: messageText }]
                    })
                });

                const data = await response.json();
                removeLoadingIndicator(loaderId);

                if (!response.ok) {
                    appendMessage('assistant', `⚠️ Erreur: ${data.message || 'Erreur serveur'}`);
                    return;
                }

                appendMessage('assistant', data.content || data.message || 'Réponse reçue.');
            } catch (err) {
                removeLoadingIndicator(loaderId);
                appendMessage('assistant', '⚠️ Impossible d\'interroger le service IA.');
            } finally {
                setLoadingState(false);
            }
        }

        function setLoadingState(isLoading) {
            isFetching = isLoading;
            if (sendBtn) sendBtn.disabled = isLoading;
            document.querySelectorAll('.chip-btn').forEach(btn => btn.disabled = isLoading);
        }

        function appendMessage(role, content, isRawHtml = false) {
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
                let formattedContent = content;
                if (!isRawHtml && typeof marked !== 'undefined') {
                    formattedContent = marked.parse(content);
                }

                wrapper.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">IA</div>
                    <div class="ai-response bg-slate-800 text-slate-100 rounded-2xl px-5 py-3.5 max-w-2xl leading-relaxed border border-slate-700/60 shadow-xs">
                        ${formattedContent}
                    </div>
                `;
            }

            chatStream.appendChild(wrapper);
            chatStream.scrollTop = chatStream.scrollHeight;
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
            chatStream.appendChild(wrapper);
            chatStream.scrollTop = chatStream.scrollHeight;
            return id;
        }

        function removeLoadingIndicator(id) {
            document.getElementById(id)?.remove();
        }

        function formatReportHtml(report) {
            if (report.type === 'stock') {
                const items = report.data.items || report.data;
                if (!Array.isArray(items) || items.length === 0) {
                    return '✅ Aucun produit en rupture ou proche du seuil d\'alerte.';
                }
                const list = items.slice(0, 10).map(i => `
                    <li class="flex justify-between items-center py-1 border-b border-slate-700/50 last:border-0">
                        <span class="font-medium text-slate-200">${i.name || i.label}</span>
                        <span class="text-xs px-2 py-0.5 rounded ${i.stock <= 0 ? 'bg-rose-900/60 text-rose-300' : 'bg-amber-900/60 text-amber-300'}">
                            ${i.stock} unités
                        </span>
                    </li>
                `).join('');
                return `<div class="font-semibold text-amber-400 mb-2">${report.title}</div><ul class="space-y-1">${list}</ul>`;
            }

            if (report.type === 'sales') {
                const total = Number(report.data.total_sales || report.data.total || 0).toLocaleString('fr-TN', { minimumFractionDigits: 2 });
                const count = report.data.count || report.data.sales_count || 0;
                return `
                    <div class="font-semibold text-blue-400 mb-2">${report.title}</div>
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <div class="bg-slate-900/60 p-2.5 rounded-lg border border-slate-700/50">
                            <div class="text-xs text-slate-400">Chiffre d'affaires</div>
                            <div class="text-base font-bold text-emerald-400">${total} TND</div>
                        </div>
                        <div class="bg-slate-900/60 p-2.5 rounded-lg border border-slate-700/50">
                            <div class="text-xs text-slate-400">Commandes</div>
                            <div class="text-base font-bold text-slate-200">${count}</div>
                        </div>
                    </div>
                `;
            }

            if (report.type === 'debts') {
                const debts = report.data.debts || report.data;
                if (!Array.isArray(debts) || debts.length === 0) {
                    return '✅ Aucun client débiteur en retard.';
                }
                const list = debts.slice(0, 10).map(d => `
                    <li class="flex justify-between items-center py-1 border-b border-slate-700/50 last:border-0">
                        <span class="font-medium text-slate-200">${d.customer_name || d.name}</span>
                        <span class="font-semibold text-rose-400">${Number(d.balance || d.debt || 0).toFixed(2)} TND</span>
                    </li>
                `).join('');
                return `<div class="font-semibold text-rose-400 mb-2">${report.title}</div><ul class="space-y-1">${list}</ul>`;
            }

            return `<pre class="text-xs bg-slate-950 p-2 rounded overflow-x-auto">${JSON.stringify(report.data, null, 2)}</pre>`;
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });
    const clearBtn = document.getElementById('clear-chat-btn');

    if (clearBtn) {
        clearBtn.addEventListener('click', async function () {
            if (!confirm('Voulez-vous vraiment effacer l\'historique ?')) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('/ai/clear', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (res.ok) {
                    // Clear DOM container
                    document.getElementById('chat-messages').innerHTML = '';
                }
            } catch (err) {
                console.error('Erreur lors de la suppression:', err);
            }
        });
    }
})();