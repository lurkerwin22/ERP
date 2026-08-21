<x-layout>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .ai-response h1, .ai-response h2, .ai-response h3 { font-weight: 700; color: #f8fafc; margin-top: 1rem; margin-bottom: 0.5rem; }
        .ai-response h2 { font-size: 1.1rem; border-bottom: 1px solid #334155; padding-bottom: 0.25rem; color: #818cf8; }
        .ai-response p { margin-bottom: 0.6rem; line-height: 1.6; color: #e2e8f0; }
        .ai-response ul, .ai-response ol { padding-left: 1.25rem; margin-top: 0.4rem; margin-bottom: 0.6rem; }
        .ai-response li { margin-bottom: 0.25rem; color: #cbd5e1; }
        .ai-response strong { color: #ffffff; font-weight: 600; }
        .ai-response table { width: 100%; border-collapse: collapse; margin: 0.75rem 0; font-size: 0.825rem; overflow-x: auto; display: block; }
        .ai-response th, .ai-response td { border: 1px solid #334155; padding: 0.5rem 0.75rem; text-align: left; }
        .ai-response th { background-color: #0f172a; color: #a5b4fc; }
        .ai-response td { background-color: #1e293b/60; color: #cbd5e1; }
    </style>

    <div class="max-w-5xl mx-auto space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <span class="text-3xl leading-none">🤖</span>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Assistant IA ERP</h1>
                    <p class="text-xs font-medium text-gray-600">Analyse intelligente des stocks, ventes et créances clients en temps réel.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                    📦 {{ $alerts['low_stock_count'] ?? 0 }} alertes stock
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-900 border border-rose-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                    💰 {{ $alerts['high_debt_customers'] ?? 0 }} créances en retard
                </span>
            </div>
        </div>

        <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 flex flex-col h-[650px] overflow-hidden">
            
            <!-- Chat Stream Area -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4 scroll-smooth">
                <!-- Welcome Message -->
                <div class="flex gap-3 text-sm">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">IA</div>
                    <div class="bg-slate-800 text-slate-100 rounded-2xl rounded-tl-xs px-4 py-3 max-w-2xl leading-relaxed border border-slate-700/60 shadow-xs">
                        Bonjour ! Je suis votre assistant ERP. Posez-moi une question sur vos ventes, l'état de votre stock, ou vos clients débiteurs.
                    </div>
                </div>

                <!-- Saved History Loop -->
                @foreach($messages as $msg)
                    <div class="flex gap-3 text-sm {{ $msg->role === 'user' ? 'justify-end' : '' }}">
                        @if($msg->role === 'assistant')
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">IA</div>
                        @endif
                        <div class="{{ $msg->role === 'user' ? 'bg-indigo-600 text-white' : 'ai-response bg-slate-800 text-slate-100 border border-slate-700/60' }} rounded-2xl px-5 py-3.5 max-w-2xl leading-relaxed shadow-xs">
                            {!! $msg->role === 'user' ? e($msg->content) : Str::markdown($msg->content) !!}
                        </div>
                        @if($msg->role === 'user')
                            <div class="w-8 h-8 rounded-lg bg-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs mt-0.5">Vous</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Input Controls -->
            <div class="p-4 border-t border-slate-800 bg-slate-950 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-slate-400 mr-1">Raccourcis :</span>

                    <button type="button" 
                            class="chip-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 text-blue-300 border border-slate-700 hover:bg-slate-800 hover:border-blue-500/50 hover:text-blue-200 transition-all cursor-pointer"
                            data-endpoint="{{ route('reports.sales') }}"
                            data-prompt="Fais-moi un résumé des ventes du mois">
                        <span>📊</span> Résumé des ventes
                    </button>

                    <button type="button" 
                            class="chip-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 text-rose-300 border border-slate-700 hover:bg-slate-800 hover:border-rose-500/50 hover:text-rose-200 transition-all cursor-pointer"
                            data-endpoint="{{ route('reports.stock') }}"
                            data-prompt="Quels produits sont presque en rupture ?">
                        <span>📦</span> Alertes ruptures
                    </button>

                    <button type="button" 
                            class="chip-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 text-amber-300 border border-slate-700 hover:bg-slate-800 hover:border-amber-500/50 hover:text-amber-200 transition-all cursor-pointer"
                            data-endpoint="{{ route('reports.debts') }}"
                            data-prompt="Quels clients ont des créances élevées ?">
                        <span>💰</span> Clients débiteurs
                    </button>
                </div>

                <form id="chat-form" data-endpoint="{{ route('ai.chat') }}" class="flex gap-2">
                    @csrf
                    <input 
                        type="text" 
                        id="user-input" 
                        placeholder="Posez votre question ou sélectionnez un raccourci..." 
                        class="flex-1 bg-slate-900 text-slate-100 placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm border border-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                        autocomplete="off"
                        required
                    >
                    <button type="submit" id="send-btn" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2 cursor-pointer shadow-xs">
                        <span>Envoyer</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            if (window.aiChatInitialized) return;
            window.aiChatInitialized = true;

            document.addEventListener('DOMContentLoaded', function () {
                const chatStream = document.getElementById('chat-messages');
                const chatForm = document.getElementById('chat-form');
                const userInput = document.getElementById('user-input');
                const sendBtn = document.getElementById('send-btn');
                const csrfToken = document.querySelector('input[name="_token"]')?.value;

                chatStream.scrollTop = chatStream.scrollHeight;

                if (typeof marked !== 'undefined') {
                    marked.setOptions({ breaks: true, gfm: true });
                }

                // Handle Chip Button Clicks
                document.querySelectorAll('.chip-btn').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        if (this.disabled) return;
                        
                        const promptText = this.getAttribute('data-prompt');
                        const endpoint = this.getAttribute('data-endpoint');

                        if (endpoint) {
                            appendMessage('user', promptText);
                            fetchReport(endpoint);
                        } else {
                            userInput.value = promptText;
                            chatForm.dispatchEvent(new Event('submit', { cancelable: true }));
                        }
                    });
                });

                // Handle Form Submit
                chatForm?.addEventListener('submit', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const text = userInput.value.trim();
                    if (!text || sendBtn.disabled) return;

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
        })();
    </script>
</x-layout>